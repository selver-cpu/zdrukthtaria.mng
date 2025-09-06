<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing.
     */
    public function run(): void
    {
        // Create basic role for testing
        DB::table('rolet')->insert([
            'rol_id' => 1,
            'emri_rolit' => 'administrator',
            'pershkrimi' => 'Role for testing',
            'data_krijimit' => now(),
        ]);
    }
}
