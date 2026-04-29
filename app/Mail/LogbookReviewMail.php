<?php

namespace App\Mail;

use App\Models\LogbookUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LogbookReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LogbookUpload $logbook,
        public string $reviewStatus
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protege MINDEF — ' . __('common.email_logbook_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.logbook-review');
    }
}
