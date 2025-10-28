<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Projektet;
use App\Models\Klientet;
use App\Models\User;
use App\Models\StatusetProjektit;

class ProjektetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klientet = Klientet::all();
        $mjeshtrit = User::whereHas('rol', function($q) {
            $q->where('emri_rolit', 'mjeshtër');
        })->get();
        $montuesit = User::whereHas('rol', function($q) {
            $q->where('emri_rolit', 'montues');
        })->get();
        $statuset = StatusetProjektit::all();

        $projektet = [
            [
                'emri_projektit' => 'Kuzhina moderne me ishull',
                'pershkrimi' => 'Kuzhina moderne me ishull dhe elektro-shtepiake të integruara',
                'data_fillimit_parashikuar' => '2025-07-01',
                'data_perfundimit_parashikuar' => '2025-07-30',
                'shenime_projekt' => 'Kuzhina moderne me ishull dhe elektro-shtepiake të integruara'
            ],
            [
                'emri_projektit' => 'Garderoba e dhomës së gjumit',
                'pershkrimi' => 'Garderobë e madhe me dyer rrethshkuese dhe ndriçim LED',
                'data_fillimit_parashikuar' => '2025-07-15',
                'data_perfundimit_parashikuar' => '2025-08-05',
                'shenime_projekt' => 'Garderobë e madhe me dyer rrethshkuese dhe ndriçim LED'
            ],
            [
                'emri_projektit' => 'Mobilim i plotë i zyrës',
                'pershkrimi' => 'Mobilim komplet i zyrës përfshirë tavolina, karrige dhe rafte',
                'data_fillimit_parashikuar' => '2025-08-01',
                'data_perfundimit_parashikuar' => '2025-09-01',
                'shenime_projekt' => 'Mobilim komplet i zyrës përfshirë tavolina, karrige dhe rafte'
            ],
            [
                'emri_projektit' => 'Biblioteka e sallonit',
                'pershkrimi' => 'Bibliotekë e madhe me rafte të rregullueshme dhe ndriçim',
                'data_fillimit_parashikuar' => '2025-08-15',
                'data_perfundimit_parashikuar' => '2025-09-15',
                'shenime_projekt' => 'Bibliotekë e madhe me rafte të rregullueshme dhe ndriçim'
            ],
        ];

        foreach ($projektet as $projekt) {
            $projekt['klient_id'] = $klientet->random()->klient_id;
            $projekt['mjeshtri_caktuar_id'] = $mjeshtrit->random()->perdorues_id;
            $projekt['montuesi_caktuar_id'] = $montuesit->random()->perdorues_id;
            $projekt['status_id'] = $statuset->random()->status_id;
            $projekt['krijues_id'] = 1; // Set the admin user as creator
            
            Projektet::create($projekt);
        }
    }
}
