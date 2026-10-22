<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application, public string $subjectLine, public string $body, public ?User $sender = null) {}

    public function envelope(): Envelope
    {
        $replyTo = setting('business.recruitment_email');

        return new Envelope(subject: $this->subjectLine, replyTo: $replyTo ? [new Address($replyTo, 'Fortress Recruitment')] : []);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.applicant-message');
    }
}
