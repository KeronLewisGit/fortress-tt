<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApplicantMessage;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $apps = $this->filtered($request)->with(['job', 'assignee'])->latest()->paginate(25)->withQueryString();

        return view('admin.applications.index', [
            'applications' => $apps,
            'jobs' => JobPosting::orderBy('title')->get(['id', 'title', 'status']),
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'stageCounts' => Application::query()->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function pipeline(Request $request)
    {
        $apps = $this->filtered($request, includeClosed: false)->with(['job', 'assignee'])->orderBy('status_changed_at')->get();

        return view('admin.applications.pipeline', [
            'columns' => collect(Application::ACTIVE_STAGES)->mapWithKeys(fn ($s) => [$s => $apps->where('status', $s)->values()]),
            'jobs' => JobPosting::orderBy('title')->get(['id', 'title', 'status']),
            'closedCounts' => ['hired' => Application::where('status', 'hired')->count(), 'rejected' => Application::where('status', 'rejected')->count()],
        ]);
    }

    public function show(Application $application)
    {
        $application->load(['job', 'assignee', 'notes.user']);

        return view('admin.applications.show', [
            'application' => $application,
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'templates' => MessageTemplate::orderBy('sort_order')->get(),
            'jobs' => JobPosting::orderBy('title')->get(['id', 'title', 'status']),
            'others' => Application::where('email', $application->email)->where('id', '!=', $application->id)->with('job')->latest()->get(),
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Application::STAGES))],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'interview_at' => ['nullable', 'date'],
            'job_posting_id' => ['nullable', 'exists:job_postings,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if (array_key_exists('rating', $data)) {
            $application->rating = $data['rating'] ?: null;
        }
        if ($request->has('assigned_to')) {
            $application->assigned_to = $data['assigned_to'] ?: null;
        }
        if ($request->has('job_posting_id')) {
            $application->job_posting_id = $data['job_posting_id'] ?: null;
        }
        if ($request->has('interview_at')) {
            $application->interview_at = $data['interview_at'] ?: null;
            if ($data['interview_at']) {
                $application->notes()->create(['user_id' => $request->user()->id, 'type' => 'interview', 'body' => 'Interview scheduled for '.$application->interview_at->format('D j M Y, g:i a')]);
            }
        }
        $application->save();

        if (! empty($data['status'])) {
            $application->moveTo($data['status'], $request->user(), $data['comment'] ?? null);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'status' => $application->status, 'label' => $application->stageLabel()]);
        }

        return back()->with('status', 'Applicant updated.');
    }

    public function note(Request $request, Application $application)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);
        $application->notes()->create(['user_id' => $request->user()->id, 'type' => 'note', 'body' => $data['body']]);

        return back()->with('status', 'Note added.');
    }

    public function message(Request $request, Application $application)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:8000'],
            'set_status' => ['nullable', Rule::in(array_keys(Application::STAGES))],
        ]);
        $subject = MessageTemplate::render($data['subject'], $application, $request->user());
        $body = MessageTemplate::render($data['body'], $application, $request->user());

        try {
            Mail::to($application->email)->send(new ApplicantMessage($application, $subject, $body, $request->user()));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['body' => 'The email could not be sent: '.$e->getMessage()])->withInput();
        }

        $application->notes()->create(['user_id' => $request->user()->id, 'type' => 'email', 'body' => $body, 'meta' => ['subject' => $subject]]);
        if (! empty($data['set_status'])) {
            $application->moveTo($data['set_status'], $request->user(), 'Moved to '.Application::STAGES[$data['set_status']].' after sending “'.$subject.'”');
        }

        return back()->with('status', 'Email sent to '.$application->email.'.');
    }

    public function resume(Application $application)
    {
        abort_unless($application->resume_path && Storage::disk('local')->exists($application->resume_path), 404);

        return Storage::disk('local')->download($application->resume_path, $application->resume_name ?: 'resume');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->with('job')->latest()->get();
        $name = 'applicants-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Applied', 'First name', 'Last name', 'Email', 'Phone', 'Area', 'Age', 'Job', 'Stage', 'Rating', 'Permit', 'Precept', 'Police cert', 'Experience (yrs)', 'Source']);
            foreach ($rows as $a) {
                fputcsv($out, [$a->reference, $a->created_at->format('Y-m-d'), $a->first_name, $a->last_name, $a->email, $a->phone, $a->area, $a->age(), $a->job?->title ?? 'General', $a->stageLabel(), $a->rating, $a->licenceLabel(), $a->has_precept ? 'Yes' : 'No', $a->has_police_certificate ? 'Yes' : 'No', $a->years_experience, Application::SOURCES[$a->source] ?? $a->source]);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv']);
    }

    public function destroy(Application $application)
    {
        if ($application->resume_path) {
            Storage::disk('local')->delete($application->resume_path);
        }
        $application->delete();

        return redirect()->route('admin.applications.index')->with('status', 'Application deleted permanently.');
    }

    private function filtered(Request $request, bool $includeClosed = true)
    {
        return Application::query()
            ->search($request->q)
            ->when($request->filled('job'), fn ($q) => $request->job === 'general' ? $q->whereNull('job_posting_id') : $q->where('job_posting_id', $request->job))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('assigned'), fn ($q) => $request->assigned === 'me' ? $q->where('assigned_to', $request->user()->id) : ($request->assigned === 'none' ? $q->whereNull('assigned_to') : $q->where('assigned_to', $request->assigned)))
            ->when($request->filled('licence'), fn ($q) => $q->where('licence_class', $request->licence))
            ->when($request->boolean('police'), fn ($q) => $q->where('has_police_certificate', true))
            ->when($request->boolean('precept'), fn ($q) => $q->where('has_precept', true))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', '>=', (int) $request->rating))
            ->when(! $includeClosed, fn ($q) => $q->active());
    }
}
