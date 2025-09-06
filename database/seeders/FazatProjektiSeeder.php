<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FazatProjekti;

class FazatProjektiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fazat = [
            [
                'emri_fazes' => 'Matjet dhe Planifikimi',
                'pershkrimi' => 'Matjet në terren dhe planifikimi i detajuar i projektit',
                'renditja' => 1
            ],
            [
                'emri_fazes' => 'Dizajni dhe Aprovimi',
                'pershkrimi' => 'Përgatitja e dizajnit dhe aprovimi nga klienti',
                'renditja' => 2
            ],
            [
                'emri_fazes' => 'Përgatitja e Materialeve',
                'pershkrimi' => 'Përgatitja dhe prerja e materialeve sipas planit',
                'renditja' => 3
            ],
            [
                'emri_fazes' => 'Montimi në Punëtori',
                'pershkrimi' => 'Montimi dhe përpunimi i elementeve në punëtori',
                'renditja' => 4
            ],
            [
                'emri_fazes' => 'Transporti',
                'pershkrimi' => 'Transporti i elementeve të gatshme te klienti',
                'renditja' => 5
            ],
            [
                'emri_fazes' => 'Montimi Final',
                'pershkrimi' => 'Montimi final i mobilierisë te klienti',
                'renditja' => 6
            ],
            [
                'emri_fazes' => 'Kontrolli dhe Dorëzimi',
                'pershkrimi' => 'Kontrolli përfundimtar dhe dorëzimi te klienti',
                'renditja' => 7
            ],
        ];

        foreach ($fazat as $faza) {
            FazatProjekti::firstOrCreate(['emri_fazes' => $faza['emri_fazes']], $faza);
        }
    }
}
