<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materialet;

class MaterialetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materialet = [
            [
                'emri_materialit' => 'Dru Ahu',
                'njesia_matese' => 'm³',
                'pershkrimi' => 'Dru ahu për mobilieri cilësore',
                'sasia_stokut' => 3.50,
                'sasia_minimale' => 0.50,
            ],
            [
                'emri_materialit' => 'Dru Lisi',
                'njesia_matese' => 'm³',
                'pershkrimi' => 'Dru lisi për mobilieri të qëndrueshme',
                'sasia_stokut' => 2.75,
                'sasia_minimale' => 0.50,
            ],
            [
                'emri_materialit' => 'MDF 18mm',
                'njesia_matese' => 'm²',
                'pershkrimi' => 'Pllakë MDF 18mm për konstruksione',
                'sasia_stokut' => 120.00,
                'sasia_minimale' => 20.00,
            ],
            [
                'emri_materialit' => 'Melaminë e Bardhë 18mm',
                'njesia_matese' => 'm²',
                'pershkrimi' => 'Pllakë melamine e bardhë për mobilieri',
                'sasia_stokut' => 90.00,
                'sasia_minimale' => 15.00,
            ],
            [
                'emri_materialit' => 'Menteshë Hidraulike',
                'njesia_matese' => 'copë',
                'pershkrimi' => 'Menteshë hidraulike për dyer',
                'sasia_stokut' => 200.00,
                'sasia_minimale' => 50.00,
            ],
            [
                'emri_materialit' => 'Doreza Inox',
                'njesia_matese' => 'copë',
                'pershkrimi' => 'Doreza inox për dyer dhe sirtarë',
                'sasia_stokut' => 300.00,
                'sasia_minimale' => 60.00,
            ],
        ];

        foreach ($materialet as $material) {
            Materialet::updateOrCreate(
                ['emri_materialit' => $material['emri_materialit']],
                $material
            );
        }
    }
}
