<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceived;
use App\Mail\NewApplicationAlert;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\JobPosting;
use App\Services\ApplicantNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobPosting::live()
            ->when($request->filled('department'), fn ($q) => $q->where('department', $request->department))
            ->when($request->filled('type'), fn ($q) => $q->where('employment_type', $request->type))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', '%'.$request->q.'%')->orWhere('location', 'like', '%'.$request->q.'%')->orWhere('summary', 'like', '%'.$request->q.'%')))
            ->orderByDesc('is_featured')->latest('published_at')->get();

        return view('pages.careers', [
            'jobs' => $jobs,
            'departments' => JobPosting::live()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department'),
            'generalOpen' => (bool) setting('careers.general_applications', true),
        ]);
    }

    public function show(JobPosting $job)
    {
        abort_unless($job->isLive(), 404);
        JobPosting::whereKey($job->id)->increment('views');

        return view('pages.job', [
            'job' => $job,
            'related' => JobPosting::live()->where('id', '!=', $job->id)->latest('published_at')->take(3)->get(),
        ]);
    }

    public function general(Request $request)
    {
        abort_unless(setting('careers.general_applications', true), 404);

        return view('pages.apply', ['job' => null, 'applicant' => $request->user('applicant')]);
    }

    public function apply(Request $request, JobPosting $job)
    {
        abort_unless($job->isLive(), 404);

        return view('pages.apply', ['job' => $job, 'applicant' => $request->user('applicant')]);
    }

    public function store(Request $request, ?JobPosting $job = null)
    {
        if ($job) {
            abort_unless($job->isLive(), 404);
        } else {
            abort_unless(setting('careers.general_applications', true), 404);
        }

        // Honeypot
        if ($request->filled('website')) {
            return redirect()->route('careers');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['required', 'date', 'before:-18 years', 'after:-70 years'],
            'nationality' => ['required', 'string', 'max:60'],
            'licence_class' => ['required', Rule::in(array_keys(Application::LICENCES))],
            'has_precept' => ['nullable', 'boolean'],
            'has_police_certificate' => ['nullable', 'boolean'],
            'has_firearm_licence' => ['nullable', 'boolean'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'education' => ['nullable', 'string', 'max:120'],
            'current_employer' => ['nullable', 'string', 'max:255'],
            'cover_letter' => ['nullable', 'string', 'max:4000'],
            'resume' => ['nullable', 'file', 'extensions:pdf,doc,docx', 'mimes:pdf,doc,docx', 'max:5120'],
            'source' => ['nullable', Rule::in(array_keys(Application::SOURCES))],
            'consent' => ['accepted'],
        ], [], ['date_of_birth' => 'date of birth', 'area' => 'town / area', 'licence_class' => "driver's permit"]);

        // One application per person per job within 30 days
        $dup = Application::where('email', strtolower($data['email']))
            ->where('job_posting_id', $job?->id)->where('created_at', '>=', now()->subDays(30))->first();
        if ($dup) {
            return redirect()->route('careers.thanks', $dup->reference)->with('duplicate', true);
        }

        unset($data['resume']);
        if ($file = $request->file('resume')) {
            $data['resume_path'] = $file->store('resumes', 'local');
            $data['resume_name'] = Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 200, '');
        }

        $application = Application::create(array_merge($data, [
            'email' => strtolower($data['email']),
            'job_posting_id' => $job?->id,
            'has_precept' => $request->boolean('has_precept'),
            'has_police_certificate' => $request->boolean('has_police_certificate'),
            'has_firearm_licence' => $request->boolean('has_firearm_licence'),
            'source' => $data['source'] ?? 'website',
            'consent' => true,
            'ip' => $request->ip(),
            'applicant_id' => $request->user('applicant')?->id ?? Applicant::where('email', strtolower($data['email']))->value('id'),
        ]));
        if ($application->applicant_id && ! $application->resume_path && $application->applicant->resume_path) {
            $application->forceFill(['resume_path' => $application->applicant->resume_path, 'resume_name' => $application->applicant->resume_name])->save();
        }
        app(ApplicantNotifier::class)->applicationReceived($application);
        $application->notes()->create(['type' => 'system', 'body' => 'Application received via website'.($job ? ' for '.$job->title : ' (general application)')]);

        $mailFailed = false;
        try {
            if (setting('careers.auto_reply', true)) {
                Mail::to($application->email)->send(new ApplicationReceived($application));
            }
        } catch (\Throwable $e) {
            report($e);
            $mailFailed = true;
            $application->notes()->create(['type' => 'system', 'body' => 'Confirmation email to applicant failed: '.$e->getMessage()]);
        }
        try {
            if ($to = notification_addresses('notifications.recruitment_email')) {
                Mail::to($to)->send(new NewApplicationAlert($application));
            }
        } catch (\Throwable $e) {
            report($e);
            $application->notes()->create(['type' => 'system', 'body' => 'Recruiter alert email failed: '.$e->getMessage()]);
        }

        return redirect()->route('careers.thanks', $application->reference)->with('mail_failed', $mailFailed);
    }

    public function thanks(string $reference)
    {
        $application = Application::where('reference', $reference)->with('job')->firstOrFail();

        return view('pages.apply-thanks', ['application' => $application]);
    }
}
