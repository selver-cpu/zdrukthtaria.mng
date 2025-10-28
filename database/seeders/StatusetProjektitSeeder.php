<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusetProjektit;

class StatusetProjektitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuset = [
            ['emri_statusit' => 'Në pritje', 'renditja' => 1, 'klasa_css' => 'warning'],
            ['emri_statusit' => 'Në progres', 'renditja' => 2, 'klasa_css' => 'primary'],
            ['emri_statusit' => 'Në pauzë', 'renditja' => 3, 'klasa_css' => 'info'],
            ['emri_statusit' => 'Përfunduar', 'renditja' => 4, 'klasa_css' => 'success'],
            ['emri_statusit' => 'Anuluar', 'renditja' => 5, 'klasa_css' => 'danger'],
        ];

        foreach ($statuset as $status) {
            StatusetProjektit::firstOrCreate(['emri_statusit' => $status['emri_statusit']], $status);
        }
    }
}
