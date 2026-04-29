<?php

namespace App\Mail;

use App\Models\StatusSurat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuratIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public StatusSurat $surat,
        public string $recipientName
    ) {
    }

    public function envelope(): Envelope
    {
        $type = $this->surat->jenis_surat === 'Surat Kuning'
            ? __('common.email_surat_kuning')
            : __('common.email_surat_biru');

        return new Envelope(
            subject: "Protege MINDEF — {$type}: {$this->surat->nama_graduan}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.surat-issued');
    }
}
