<?php

namespace App\Mail;

use App\Models\KewanganElaun;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AllowancePaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public KewanganElaun $payment,
        public string $talentName,
        public string $status
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protege MINDEF — ' . __('common.email_payment_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.allowance-payment');
    }
}
