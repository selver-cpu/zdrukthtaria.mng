<?php

namespace App\Mail;

use App\Models\Projektet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class ProjektNjoftim extends Mailable
{
    use Queueable, SerializesModels;

    public $projekti;
    public $user;
    public $mesazhi;
    public $dokumentet;

    /**
     * Create a new message instance.
     */
    public function __construct(Projektet $projekti, User $user, string $mesazhi, $dokumentet = [])
    {
        $this->projekti = $projekti;
        $this->user = $user;
        $this->mesazhi = $mesazhi;
        $this->dokumentet = $dokumentet;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Njoftim për Projektin: ' . $this->projekti->emri_projektit,
            from: new \Illuminate\Mail\Mailables\Address(
                env('MAIL_FROM_ADDRESS', 'carpentry@app.com'),
                env('MAIL_FROM_NAME', 'Carpentry Design App')
            ),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    env('MAIL_FROM_ADDRESS', 'carpentry@app.com'),
                    env('MAIL_FROM_NAME', 'Carpentry Design App')
                ),
            ],
            tags: ['projekt', 'njoftim'],
            metadata: [
                'projekt_id' => $this->projekti->projekt_id,
                'user_id' => $this->user->perdorues_id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.projekt-njoftim',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Bashkangjit dokumentet e projektit nëse ka
        if (is_array($this->dokumentet) || $this->dokumentet instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($this->dokumentet as $dokument) {
                try {
                    // Përdor direkt rruga_skedarit nga baza e të dhënave
                    if (!empty($dokument->rruga_skedarit)) {
                        // Kontrollo nëse skedari ekziston në disk
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dokument->rruga_skedarit)) {
                            // Nxjerr emrin e skedarit nga rruga
                            $fileName = basename($dokument->rruga_skedarit);
                            
                            // Hiq timestamp-in nga fillimi i emrit të skedarit nëse ekziston
                            if (preg_match('/^\d+_(.+)$/', $fileName, $matches)) {
                                $displayName = $matches[1];
                            } else {
                                $displayName = $fileName;
                            }
                            
                            // Përdor fromStorageDisk për të specifikuar disk-un dhe rruga relative
                            $attachments[] = Attachment::fromStorageDisk('public', $dokument->rruga_skedarit)
                                ->as($displayName);
                                
                            \Illuminate\Support\Facades\Log::info('Dokument u bashkëngjit: ' . $dokument->rruga_skedarit);
                        } else {
                            \Illuminate\Support\Facades\Log::warning('Dokumenti nuk u gjet: ' . $dokument->rruga_skedarit);
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning('Dokumenti nuk ka rruga_skedarit: ID ' . $dokument->dokument_id);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gabim gjatë bashkëngjitjes së dokumentit: ' . $e->getMessage());
                }
            }
        }
        
        return $attachments;
    }
}
