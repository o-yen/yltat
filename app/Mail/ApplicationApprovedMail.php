<?php

namespace App\Mail;

use App\Models\Talent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Talent $talent,
        public string $temporaryPassword
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protege MINDEF — ' . __('common.email_approved_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
        );
    }
}
