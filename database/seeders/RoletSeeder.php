<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rolet;

class RoletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolet = [
            ['emri_rolit' => 'administrator'],
            ['emri_rolit' => 'menaxher'],
            ['emri_rolit' => 'mjeshtër'],
            ['emri_rolit' => 'montues'],
        ];

        foreach ($rolet as $rol) {
            Rolet::firstOrCreate(['emri_rolit' => $rol['emri_rolit']], $rol);
        }
    }
}
