<?php

namespace App\Services;

use App\Mail\ApplicantUpdate;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;

/**
 * Writes an in-portal notification (when the applicant has an account) and emails the applicant.
 * Mail failures are recorded on the application timeline instead of breaking the admin action.
 */
class ApplicantNotifier
{
    public function statusChanged(Application $application): void
    {
        [$title, $body] = match ($application->publicStatus()) {
            'review' => ['Your application is being reviewed', 'Our recruitment team is reviewing your application'.$this->forJob($application).'. We will contact you about the next steps.'],
            'interview' => ['You have been invited to interview', $application->interview_at
                ? 'Your interview'.$this->forJob($application).' is scheduled for '.$application->interview_at->format('l j F Y \a\t g:i a').'.'
                : 'Congratulations – you have progressed to the interview stage'.$this->forJob($application).'. Sign in to your applicant portal to choose an interview time.'],
            'accepted' => ['Your application has been accepted', 'Congratulations! Your application'.$this->forJob($application).' has been accepted. Our team will contact you about onboarding, documents and your training start date.'],
            'rejected' => ['Update on your application', 'Thank you for your interest in Fortress Security Services. After careful consideration we will not be progressing your application'.$this->forJob($application).' on this occasion. We keep applications on file for six months and you are welcome to apply for future openings.'],
            'withdrawn' => ['Your application has been withdrawn', 'Your application'.$this->forJob($application).' has been marked as withdrawn.'],
            default => ['Application received', 'We have received your application'.$this->forJob($application).'.'],
        };

        $this->send($application, 'status', $title, $body);
    }

    public function interviewScheduled(Application $application, bool $bookedByApplicant = false): void
    {
        if (! $application->interview_at) {
            return;
        }
        $when = $application->interview_at->format('l j F Y \a\t g:i a');
        $where = $application->interview_location ?: 'our head office, 8 Desaine Street, St. Augustine';
        $title = $bookedByApplicant ? 'Interview booked' : 'Your interview has been scheduled';
        $body = 'Your interview'.$this->forJob($application).' is on '.$when.' at '.$where.'. Please bring two forms of ID, your Police Certificate of Character, driver\'s permit (if any) and original certificates.';
        $this->send($application, 'interview', $title, $body);
    }

    public function interviewCleared(Application $application): void
    {
        $this->send($application, 'interview', 'Interview date removed', 'The interview date for your application'.$this->forJob($application).' has been removed. We will be in touch to arrange a new time.');
    }

    /** Admin email already went out via ApplicantMessage; just mirror it into the portal inbox. */
    public function messageSent(Application $application, string $subject, string $body): void
    {
        $this->record($application, 'message', $subject, $body);
    }

    public function applicationReceived(Application $application): void
    {
        $this->record($application, 'system', 'Application received – '.$application->reference, 'We have received your application'.$this->forJob($application).'. Track its progress here.');
    }

    private function send(Application $application, string $type, string $title, string $body): void
    {
        $this->record($application, $type, $title, $body);
        try {
            Mail::to($application->email)->send(new ApplicantUpdate($application, $title, $body));
        } catch (\Throwable $e) {
            report($e);
            $application->notes()->create(['type' => 'system', 'body' => 'Applicant notification email failed ('.$title.'): '.$e->getMessage()]);
        }
    }

    private function record(Application $application, string $type, string $title, ?string $body): void
    {
        if (! $application->applicant_id) {
            return;
        }
        $application->applicant->notifications()->create([
            'application_id' => $application->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => route('portal.applications.show', $application->reference),
        ]);
    }

    private function forJob(Application $application): string
    {
        return $application->job ? ' for '.$application->job->title : '';
    }
}
