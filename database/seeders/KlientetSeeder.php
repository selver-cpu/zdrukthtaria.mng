<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Klientet;

class KlientetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klientet = [
            [
                'person_kontakti' => 'Arben Krasniqi',
                'telefon_kontakt' => '+383 44 123 456',
                'email_kontakt' => 'arben.k@example.com',
                'adresa_faktura' => 'Rr. UCK, Nr. 25',
                'qyteti' => 'Prishtina',
                'kodi_postal' => '10000',
                'shteti' => 'Kosova',
                'shenime' => 'Klient i rregullt, preferon dizajne moderne'
            ],
            [
                'person_kontakti' => 'Fatmir Berisha',
                'telefon_kontakt' => '+383 45 234 567',
                'email_kontakt' => 'fatmir.b@example.com',
                'adresa_faktura' => 'Rr. Dardania, Nr. 12',
                'qyteti' => 'Prizren',
                'kodi_postal' => '20000',
                'shteti' => 'Kosova',
                'shenime' => 'Klient i ri, ka kërkesa specifike për ngjyrat'
            ],
            [
                'person_kontakti' => 'Vlora Gashi',
                'telefon_kontakt' => '+383 49 345 678',
                'email_kontakt' => 'vlora.g@example.com',
                'adresa_faktura' => 'Rr. Tirana, Nr. 45',
                'qyteti' => 'Peja',
                'kodi_postal' => '30000',
                'shteti' => 'Kosova',
                'shenime' => 'Kërkon punë cilësore, buxhet fleksibël'
            ],
            [
                'person_kontakti' => 'Driton Hoxha',
                'telefon_kontakt' => '+383 43 456 789',
                'email_kontakt' => 'driton.h@example.com',
                'adresa_faktura' => 'Rr. Skenderbeu, Nr. 78',
                'qyteti' => 'Gjakova',
                'kodi_postal' => '50000',
                'shteti' => 'Kosova',
                'shenime' => 'Preferon materiale tradicionale dhe dizajn klasik'
            ],
        ];

        foreach ($klientet as $klient) {
            Klientet::create($klient);
        }
    }
}
