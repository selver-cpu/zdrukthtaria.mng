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
                'pershkrimi' => 'Dru ahu për mobilieri cilësore'
            ],
            [
                'emri_materialit' => 'Dru Lisi',
                'njesia_matese' => 'm³',
                'pershkrimi' => 'Dru lisi për mobilieri të qëndrueshme'
            ],
            [
                'emri_materialit' => 'MDF 18mm',
                'njesia_matese' => 'm²',
                'pershkrimi' => 'Pllakë MDF 18mm për konstruksione'
            ],
            [
                'emri_materialit' => 'Melaminë e Bardhë 18mm',
                'njesia_matese' => 'm²',
                'pershkrimi' => 'Pllakë melamine e bardhë për mobilieri'
            ],
            [
                'emri_materialit' => 'Menteshë Hidraulike',
                'njesia_matese' => 'copë',
                'pershkrimi' => 'Menteshë hidraulike për dyer'
            ],
            [
                'emri_materialit' => 'Doreza Inox',
                'njesia_matese' => 'copë',
                'pershkrimi' => 'Doreza inox për dyer dhe sirtarë'
            ],
        ];

        foreach ($materialet as $material) {
            Materialet::create($material);
        }
    }
}
