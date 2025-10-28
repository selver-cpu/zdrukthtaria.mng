<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rolet;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the 'administrator' role
        $rol = Rolet::where('emri_rolit', 'administrator')->first();

        // If the role exists, create the user
        if ($rol) {
            // Create or update the admin user
            User::updateOrCreate(
                ['email' => 'selver.kryeziu@gmail.com'],
                [
                    'rol_id' => $rol->rol_id,
                    'emri' => 'Selver',
                    'mbiemri' => 'Kryeziu',
                    'fjalekalimi_hash' => Hash::make('Veli2024@'),
                    'aktiv' => 1,
                ]
            );
            
            // Keep the example admin user as well
            User::updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'rol_id' => $rol->rol_id,
                    'emri' => 'Admin',
                    'mbiemri' => 'User',
                    'fjalekalimi_hash' => Hash::make('password'),
                    'aktiv' => 1,
                ]
            );
        }
    }
}
