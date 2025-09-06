<?php

namespace App\Listeners;

use App\Events\NjoftimIRi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessoNjoftimin implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NjoftimIRi $event): void
    {
        $njoftim = $event->njoftim;
        $perdorues = $njoftim->perdorues;

        switch ($njoftim->lloji_njoftimit) {
            case 'email':
                if ($perdorues && $perdorues->email) {
                    try {
                        Mail::raw($njoftim->mesazhi, function ($message) use ($perdorues) {
                            $message->to($perdorues->email)
                                   ->subject('Njoftim i ri nga sistemi');
                        });
                        Log::info("Email u dërgua me sukses për përdoruesin {$perdorues->email}");
                    } catch (\Exception $e) {
                        Log::error("Gabim në dërgimin e email-it: " . $e->getMessage());
                    }
                }
                break;

            case 'sms':
                if ($perdorues && $perdorues->telefon) {
                    try {
                        // Këtu do të implementohet logjika e dërgimit të SMS
                        // duke përdorur një shërbim si Twilio ose Vonage
                        Log::info("SMS do të dërgohet në numrin {$perdorues->telefon}");
                    } catch (\Exception $e) {
                        Log::error("Gabim në dërgimin e SMS-së: " . $e->getMessage());
                    }
                }
                break;

            case 'system':
                // Njoftimet e sistemit janë automatikisht të disponueshme
                // përmes broadcast-it të eventit NjoftimIRi
                break;
        }
    }
}
