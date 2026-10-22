<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Website enquiry: '.($this->enquiry->service ?: 'General').' – '.$this->enquiry->name, replyTo: [$this->enquiry->email]);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.enquiry');
    }
}
