<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rolet;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        // Gjej rolet
        $adminRole = Rolet::where('emri_rolit', 'administrator')->first();
        $mjeshterRole = Rolet::where('emri_rolit', 'mjeshtër')->first();

        // Krijo/përditëso përdoruesin admin
        User::updateOrCreate(
            ['email' => 'selver.kryeziu@gmail.com'],
            [
            'emri' => 'Selver',
            'mbiemri' => 'Kryeziu',
            'email' => 'selver.kryeziu@gmail.com',
            'fjalekalimi_hash' => Hash::make('Veli2024@'),
            'rol_id' => $adminRole->rol_id,
            'aktiv' => true
        ]);

        // Krijo/përditëso përdoruesin mjeshtër
        User::updateOrCreate(
            ['email' => 'selver.kryeziu@yahoo.com'],
            [
            'emri' => 'Selver',
            'mbiemri' => 'Kryeziu',
            'email' => 'selver.kryeziu@yahoo.com',
            'fjalekalimi_hash' => Hash::make('Veli123@'),
            'rol_id' => $mjeshterRole->rol_id,
            'aktiv' => true
        ]);
    }
}
