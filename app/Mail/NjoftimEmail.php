<?php

namespace App\Mail;

use App\Models\Njoftimet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NjoftimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $njoftim;

    /**
     * Create a new message instance.
     */
    public function __construct(Njoftimet $njoftim)
    {
        $this->njoftim = $njoftim;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Njoftim i Ri - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.njoftim',
            with: [
                'mesazhi' => $this->njoftim->mesazhi,
                'projekt' => $this->njoftim->projekt,
                'data' => $this->njoftim->data_krijimit,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
