<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DokumentetProjekti;
use App\Models\Projekti;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DokumentetProjektiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sigurohemi që direktoria e dokumenteve ekziston
        Storage::disk('public')->makeDirectory('dokumentet_projekti');
        
        // Marrim të gjitha projektet
        $projektet = Projekti::all();
        
        if ($projektet->isEmpty()) {
            $this->command->info('Nuk ka projekte në bazën e të dhënave. Ju lutemi ekzekutoni së pari seeder-in e projekteve.');
            return;
        }
        
        // Marrim përdoruesit për t'i përdorur si ngarkues të dokumenteve
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('Nuk ka përdorues në bazën e të dhënave. Ju lutemi ekzekutoni së pari seeder-in e përdoruesve.');
            return;
        }
        
        // Fshijmë të dhënat ekzistuese
        DB::table('dokumentet_projekti')->truncate();
        
        // Llojet e dokumenteve që do të simulojmë
        $llojetDokumenteve = [
            [
                'emri' => 'Vizatim teknik.pdf',
                'lloji' => 'application/pdf',
                'madhesia' => 1024 * 1024, // 1MB
                'kategoria' => 'vizatim'
            ],
            [
                'emri' => 'Dimensionet e projektit.pdf',
                'lloji' => 'application/pdf',
                'madhesia' => (int)(1.5 * 1024 * 1024), // 1.5MB
                'kategoria' => 'dimension'
            ],
            [
                'emri' => 'Lista e materialeve.xlsx',
                'lloji' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'madhesia' => 512 * 1024, // 512KB
                'kategoria' => 'excel'
            ],
            [
                'emri' => 'Modeli 3D.stl',
                'lloji' => 'application/octet-stream',
                'madhesia' => 2 * 1024 * 1024, // 2MB
                'kategoria' => '3d_model'
            ],
            [
                'emri' => 'Foto e projektit.jpg',
                'lloji' => 'image/jpeg',
                'madhesia' => 3 * 1024 * 1024, // 3MB
                'kategoria' => 'foto'
            ],
            [
                'emri' => 'Dokumentacion shtesë.docx',
                'lloji' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'madhesia' => (int)(1.2 * 1024 * 1024), // 1.2MB
                'kategoria' => 'tjeter'
            ],
        ];
        
        // Për çdo projekt, shtojmë 2-5 dokumente
        foreach ($projektet as $projekt) {
            $numriDokumenteve = rand(2, 5);
            
            for ($i = 0; $i < $numriDokumenteve; $i++) {
                // Zgjedhim një lloj dokumenti rastësisht
                $dokument = $llojetDokumenteve[array_rand($llojetDokumenteve)];
                
                // Zgjedhim një përdorues rastësisht si ngarkues
                $user = $users->random();
                
                // Krijojmë një emër unik për skedarin
                $timestamp = time() - rand(0, 30 * 24 * 60 * 60); // Një datë brenda 30 ditëve të fundit
                $filename = $timestamp . '_' . $dokument['emri'];
                $path = 'dokumentet_projekti/' . $projekt->projekt_id . '/' . $filename;
                
                // Krijojmë direktorinë për projektin nëse nuk ekziston
                Storage::disk('public')->makeDirectory('dokumentet_projekti/' . $projekt->projekt_id);
                
                // Krijojmë një skedar të zbrazët për të simuluar dokumentin
                Storage::disk('public')->put($path, 'Ky është një skedar test për qëllime demonstrimi.');
                
                // Krijojmë rekordin në bazën e të dhënave
                DokumentetProjekti::create([
                    'projekt_id' => $projekt->projekt_id,
                    'emri_skedarit' => $dokument['emri'],
                    'rruga_skedarit' => $path,
                    'lloji_skedarit' => $dokument['lloji'],
                    'madhesia_skedarit' => $dokument['madhesia'],
                    'pershkrimi' => 'Ky është një dokument test për projektin ' . $projekt->emri_projektit,
                    'kategoria' => $dokument['kategoria'],
                    'perdorues_id_ngarkues' => $user->perdorues_id,
                    'created_at' => Carbon::createFromTimestamp($timestamp),
                    'updated_at' => Carbon::createFromTimestamp($timestamp),
                ]);
            }
        }
        
        $this->command->info('Janë krijuar me sukses ' . DokumentetProjekti::count() . ' dokumente test.');
    }
}
