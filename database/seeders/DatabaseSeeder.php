<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoletSeeder;
use Database\Seeders\StatusetProjektitSeeder;
use Database\Seeders\PerdoruesitSeeder;
use Database\Seeders\TestUsersSeeder;
use Database\Seeders\DokumentetProjektiSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoletSeeder::class,
            StatusetProjektitSeeder::class,
            AdminUserSeeder::class,
            PerdoruesitSeeder::class,
            TestUsersSeeder::class,
            MaterialetSeeder::class,
            FazatProjektiSeeder::class,
            KlientetSeeder::class,
            ProjektetSeeder::class,
            DokumentetProjektiSeeder::class,
        ]);
    }
}
