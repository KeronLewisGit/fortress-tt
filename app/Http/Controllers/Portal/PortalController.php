<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InterviewSlot;
use App\Models\JobPosting;
use App\Services\ApplicantNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $applicant = $request->user('applicant');
        $applications = $applicant->applications()->with(['job', 'interviewSlot'])->get();

        return view('portal.dashboard', [
            'applicant' => $applicant,
            'applications' => $applications,
            'needsAction' => $applications->filter(fn ($a) => $a->canBookInterview()),
            'upcoming' => $applications->filter(fn ($a) => $a->interview_at && $a->interview_at->isFuture())->sortBy('interview_at'),
            'notifications' => $applicant->notifications()->take(5)->get(),
            'unread' => $applicant->unreadCount(),
            'jobs' => JobPosting::live()->whereNotIn('id', $applications->pluck('job_posting_id')->filter())->latest('published_at')->take(2)->get(),
        ]);
    }

    public function show(Request $request, string $reference)
    {
        $application = $this->own($request, $reference)->load(['job', 'interviewSlot']);

        return view('portal.application', [
            'application' => $application,
            'timeline' => $application->publicNotes()->latest()->get(),
            'slots' => $application->canBookInterview() ? InterviewSlot::available()->take(30)->get() : collect(),
        ]);
    }

    public function book(Request $request, string $reference)
    {
        $application = $this->own($request, $reference);
        abort_unless($application->canBookInterview(), 403, 'This application is not open for interview booking.');
        $data = $request->validate(['slot' => ['required', 'exists:interview_slots,id']]);

        $slot = InterviewSlot::withCount('bookings')->lockForUpdate()->findOrFail($data['slot']);
        if (! $slot->isBookable()) {
            return back()->withErrors(['slot' => 'That time has just been taken – please choose another.']);
        }

        $application->forceFill(['interview_at' => $slot->starts_at, 'interview_slot_id' => $slot->id, 'interview_location' => $slot->location])->save();
        $application->notes()->create(['type' => 'interview', 'body' => 'Interview booked by applicant for '.$slot->starts_at->format('D j M Y, g:i a').' at '.$slot->location]);
        app(ApplicantNotifier::class)->interviewScheduled($application, bookedByApplicant: true);

        return redirect()->route('portal.applications.show', $reference)->with('status', 'Your interview is booked for '.$slot->starts_at->format('l j F \a\t g:i a').'.');
    }

    public function notifications(Request $request)
    {
        $applicant = $request->user('applicant');
        $notifications = $applicant->notifications()->with('application.job')->paginate(20);
        $applicant->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('portal.notifications', ['notifications' => $notifications]);
    }

    public function profile(Request $request)
    {
        return view('portal.profile', ['applicant' => $request->user('applicant')]);
    }

    public function updateProfile(Request $request)
    {
        $applicant = $request->user('applicant');
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:40'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:applicant'],
            'password' => ['nullable', 'confirmed', PasswordRule::min(8)],
        ]);
        $applicant->fill(['first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'phone' => $data['phone'] ?? null]);
        if (! empty($data['password'])) {
            $applicant->password = $data['password'];
        }
        $applicant->save();

        return back()->with('status', 'Profile updated.');
    }

    public function storeResume(Request $request)
    {
        $applicant = $request->user('applicant');
        $request->validate(['resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:5120']]);
        $file = $request->file('resume');
        if ($applicant->resume_path) {
            Storage::disk('local')->delete($applicant->resume_path);
        }
        $applicant->forceFill([
            'resume_path' => $file->store('resumes/applicants', 'local'),
            'resume_name' => substr(basename($file->getClientOriginalName()), 0, 255),
            'resume_updated_at' => now(),
        ])->save();

        // Attach the new CV to active applications that have none, so recruiters see it straight away.
        $applicant->applications()->active()->whereNull('resume_path')->update(['resume_path' => $applicant->resume_path, 'resume_name' => $applicant->resume_name]);

        return back()->with('status', 'Your CV has been saved.');
    }

    public function downloadResume(Request $request)
    {
        $applicant = $request->user('applicant');
        abort_unless($applicant->resume_path && Storage::disk('local')->exists($applicant->resume_path), 404);

        return Storage::disk('local')->download($applicant->resume_path, $applicant->resume_name ?: 'resume');
    }

    public function destroyResume(Request $request)
    {
        $applicant = $request->user('applicant');
        if ($applicant->resume_path) {
            Storage::disk('local')->delete($applicant->resume_path);
            $applicant->forceFill(['resume_path' => null, 'resume_name' => null, 'resume_updated_at' => null])->save();
        }

        return back()->with('status', 'Your CV has been removed from your profile.');
    }

    private function own(Request $request, string $reference): Application
    {
        return $request->user('applicant')->applications()->where('reference', $reference)->firstOrFail();
    }
}
