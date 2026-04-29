<?php

namespace App\Mail;

use App\Models\Talent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TalentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Talent $talent,
        public User $user
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protege MINDEF — ' . __('common.email_talent_welcome_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.talent-welcome',
        );
    }
}
