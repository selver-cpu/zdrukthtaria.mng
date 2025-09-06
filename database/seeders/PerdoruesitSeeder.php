<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rolet;
use Illuminate\Support\Facades\Hash;

class PerdoruesitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Merr rolet nga databaza
        $adminRole = Rolet::where('emri_rolit', 'administrator')->first();
        $menaxherRole = Rolet::where('emri_rolit', 'menaxher')->first();
        $meshtriRole = Rolet::where('emri_rolit', 'mjeshtër')->first();
        $montuesRole = Rolet::where('emri_rolit', 'montues')->first();

        // Krijo përdorues admin
        User::firstOrCreate(
            ['email' => 'selver.kryeziu@gmail.com'],
            [
            'rol_id' => $adminRole->rol_id,
            'emri' => 'Selver',
            'mbiemri' => 'Kryeziu',
            'email' => 'selver.kryeziu@gmail.com',
            'fjalekalimi_hash' => Hash::make('Veli2024@'),
            'telefon' => '+355691234567',
            'adresa' => 'Tiranë, Shqipëri',
            'aktiv' => true,
        ]);

        // Krijo përdorues menaxher
        User::firstOrCreate(
            ['email' => 'menaxher@carpentry.com'],
            [
            'rol_id' => $menaxherRole->rol_id,
            'emri' => 'Menaxher',
            'mbiemri' => 'Test',
            'email' => 'menaxher@carpentry.com',
            'fjalekalimi_hash' => Hash::make('password'),
            'telefon' => '+355691234568',
            'adresa' => 'Tiranë, Shqipëri',
            'aktiv' => true,
        ]);

        // Krijo përdorues mjeshtër
        User::firstOrCreate(
            ['email' => 'mjeshtri@carpentry.com'],
            [
            'rol_id' => $meshtriRole->rol_id,
            'emri' => 'Mjeshtër',
            'mbiemri' => 'Test',
            'email' => 'mjeshtri@carpentry.com',
            'fjalekalimi_hash' => Hash::make('password'),
            'telefon' => '+355691234569',
            'adresa' => 'Tiranë, Shqipëri',
            'aktiv' => true,
        ]);

        // Krijo përdorues montues
        User::firstOrCreate(
            ['email' => 'montues@carpentry.com'],
            [
            'rol_id' => $montuesRole->rol_id,
            'emri' => 'Montues',
            'mbiemri' => 'Test',
            'email' => 'montues@carpentry.com',
            'fjalekalimi_hash' => Hash::make('password'),
            'telefon' => '+355691234570',
            'adresa' => 'Tiranë, Shqipëri',
            'aktiv' => true,
        ]);
    }
}
