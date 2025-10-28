<?php

namespace App\Services;

use App\Mail\ProjektNjoftim;
use App\Models\Njoftimet;
use App\Models\Projektet;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Dërgon njoftim për projektin dhe email nëse është e mundur
     *
     * @param Projektet $projekti Projekti për të cilin dërgohet njoftimi
     * @param string $mesazhi Mesazhi i njoftimit
     * @param array $perdoruesit Lista e ID-ve të përdoruesve për të njoftuar
     * @param bool $sendEmail Nëse duhet dërguar email
     * @return void
     */
    public function sendProjectNotification(Projektet $projekti, string $mesazhi, array $perdoruesit = [], bool $sendEmail = true)
    {
        // Nëse nuk janë specifikuar përdoruesit, njofto mjeshtrin dhe montuesin e caktuar
        if (empty($perdoruesit)) {
            if ($projekti->mjeshtri_caktuar_id) {
                $perdoruesit[] = $projekti->mjeshtri_caktuar_id;
            }
            if ($projekti->montuesi_caktuar_id) {
                $perdoruesit[] = $projekti->montuesi_caktuar_id;
            }
        }

        // Krijo njoftimet për secilin përdorues
        foreach ($perdoruesit as $perdorues_id) {
            // Krijo njoftimin në sistem
            Njoftimet::create([
                'perdorues_id' => $perdorues_id,
                'projekt_id' => $projekti->projekt_id,
                'mesazhi' => $mesazhi,
                'lloji_njoftimit' => 'system',
                'data_krijimit' => now(),
                'lexuar' => false
            ]);
            
            // Dërgo email nëse është kërkuar
            if ($sendEmail) {
                try {
                    $user = User::find($perdorues_id);
                    if ($user && $user->email) {
                        $this->sendProjectEmail($projekti, $user, $mesazhi);
                    }
                } catch (\Exception $e) {
                    Log::error('Gabim gjatë dërgimit të email-it: ' . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Dërgon email për projektin
     *
     * @param Projektet $projekti Projekti për të cilin dërgohet email-i
     * @param User $user Përdoruesi që merr email-in
     * @param string $mesazhi Mesazhi i email-it
     * @return void
     */
    public function sendProjectEmail(Projektet $projekti, User $user, string $mesazhi)
    {
        try {
            // Merr dokumentet e projektit për t'i bashkangjitur
            $dokumentet = $projekti->dokumentet ?? [];
            
            // Dërgo email-in
            Mail::to($user->email)
                ->send(new ProjektNjoftim($projekti, $user, $mesazhi, $dokumentet));
                
            Log::info('Email u dërgua me sukses për përdoruesin: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Gabim gjatë dërgimit të email-it: ' . $e->getMessage());
        }
    }

    /**
     * Njofton krijimin e një projekti të ri
     *
     * @param Projektet $projekti Projekti i sapo krijuar
     * @return void
     */
    public function notifyProjectCreation(Projektet $projekti)
    {
        // Gjej të gjithë menaxherët
        $menaxheret = User::where('rol_id', 2)->get(); // Rol ID 2 për menaxher
        
        $mesazhi = sprintf(
            'Projekt i ri u krijua: "%s" për klientin "%s"',
            $projekti->emri_projektit,
            $projekti->klient->person_kontakti ?? 'N/A'
        );

        // Njofto menaxherët
        foreach ($menaxheret as $menaxher) {
            // Krijo njoftimin në sistem
            Njoftimet::create([
                'perdorues_id' => $menaxher->perdorues_id,
                'projekt_id' => $projekti->projekt_id,
                'mesazhi' => $mesazhi,
                'lloji_njoftimit' => 'system',
                'data_krijimit' => now(),
                'lexuar' => false
            ]);
            
            // Dërgo email menaxherit
            if ($menaxher->email) {
                $this->sendProjectEmail($projekti, $menaxher, $mesazhi);
            }
        }

        // Nëse ka mjeshtër të caktuar, njoftoje
        if ($projekti->mjeshtri_caktuar_id) {
            $mjeshtriMesazhi = 'Ju jeni caktuar si mjeshtër për projektin: ' . $projekti->emri_projektit;
            $this->sendProjectNotification(
                $projekti,
                $mjeshtriMesazhi,
                [$projekti->mjeshtri_caktuar_id],
                true // Dërgo email
            );
        }

        // Nëse ka montues të caktuar, njoftoje
        if ($projekti->montuesi_caktuar_id) {
            $montuesiMesazhi = 'Ju jeni caktuar si montues për projektin: ' . $projekti->emri_projektit;
            $this->sendProjectNotification(
                $projekti,
                $montuesiMesazhi,
                [$projekti->montuesi_caktuar_id],
                true // Dërgo email
            );
        }
    }
}
