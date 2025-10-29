<?php

namespace App\Listeners;

use App\Events\NjoftimIRi;
use App\Mail\NjoftimEmail;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessoNjoftimin // implements ShouldQueue
{
    // use InteractsWithQueue;

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
                        Mail::to($perdorues->email)->send(new NjoftimEmail($njoftim));
                        Log::info("Email u dërgua me sukses për përdoruesin {$perdorues->email}");
                    } catch (\Exception $e) {
                        Log::error("Gabim në dërgimin e email-it: " . $e->getMessage());
                    }
                }
                break;

            case 'sms':
                if ($perdorues && $perdorues->telefon) {
                    try {
                        $smsService = new SmsService();
                        
                        // Shkurto mesazhin për SMS (max 160 karaktere)
                        $smsMessage = substr($njoftim->mesazhi, 0, 150);
                        if (strlen($njoftim->mesazhi) > 150) {
                            $smsMessage .= '...';
                        }
                        
                        $success = $smsService->send($perdorues->telefon, $smsMessage);
                        
                        if ($success) {
                            Log::info("SMS u dërgua me sukses në numrin {$perdorues->telefon}");
                        } else {
                            Log::warning("SMS nuk u dërgua në numrin {$perdorues->telefon}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Gabim në dërgimin e SMS-së: " . $e->getMessage());
                    }
                }
                break;

            case 'system':
                // Njoftimet e sistemit janë automatikisht të disponueshme
                // përmes broadcast-it të eventit NjoftimIRi
                Log::info("Njoftim sistemi u krijua për përdoruesin {$perdorues->emri}");
                break;
        }
    }
}
