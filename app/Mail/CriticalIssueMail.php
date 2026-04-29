<?php

namespace App\Mail;

use App\Models\IsuRisiko;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CriticalIssueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public IsuRisiko $issue
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protege MINDEF — ' . __('common.email_critical_issue_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.critical-issue');
    }
}
