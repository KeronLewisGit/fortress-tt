<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewApplicationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New applicant: '.$this->application->full_name.' – '.($this->application->job?->title ?? 'General application'),
            replyTo: [$this->application->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-application');
    }
}
