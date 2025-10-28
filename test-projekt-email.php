<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Duke testuar dërgimin e emailit të projektit...\n";
    
    // Gjej një projekt ekzistues për testim
    $projekti = \App\Models\Projektet::with(['klient', 'dokumentet'])->first();
    
    if (!$projekti) {
        echo "Nuk u gjet asnjë projekt për testim!\n";
        exit;
    }
    
    echo "Projekti i gjetur: " . $projekti->emri_projektit . "\n";
    echo "Klienti: " . ($projekti->klient->person_kontakti ?? 'N/A') . "\n";
    echo "Numri i dokumenteve: " . count($projekti->dokumentet ?? []) . "\n\n";
    
    // Shfaq informacion për secilin dokument
    if (count($projekti->dokumentet) > 0) {
        echo "Detajet e dokumenteve:\n";
        foreach ($projekti->dokumentet as $index => $dokument) {
            echo "Dokumenti #{$index}:\n";
            echo "  ID: {$dokument->dokument_id}\n";
            echo "  Emri: {$dokument->emri_skedarit}\n";
            echo "  Rruga: {$dokument->rruga_skedarit}\n";
            
            // Kontrollo nëse skedari ekziston
            $filePath = 'dokumentet_projekti/' . $dokument->dokument_id . '/' . $dokument->emri_skedarit;
            $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath);
            echo "  Rruga e plotë: {$filePath}\n";
            echo "  Ekziston: " . ($fileExists ? 'Po' : 'Jo') . "\n";
            
            // Kontrollo rruga alternative
            if (!$fileExists && !empty($dokument->rruga_skedarit)) {
                $alternativeExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($dokument->rruga_skedarit);
                echo "  Ekziston (rruga alternative): " . ($alternativeExists ? 'Po' : 'Jo') . "\n";
            }
            echo "\n";
        }
    }
    
    // Gjej një përdorues për testim
    $user = \App\Models\User::first();
    
    if (!$user) {
        echo "Nuk u gjet asnjë përdorues për testim!\n";
        exit;
    }
    
    echo "Përdoruesi i gjetur: " . $user->emri . " " . $user->mbiemri . "\n";
    echo "Email: " . $user->email . "\n\n";
    
    // Dërgo emailin e projektit
    $mesazhi = "Ky është një testim i emailit të projektit me të dhëna reale.";
    
    echo "Duke dërguar emailin...\n";
    \Illuminate\Support\Facades\Mail::to('selver.kryeziu@gmail.com')
        ->send(new \App\Mail\ProjektNjoftim($projekti, $user, $mesazhi, $projekti->dokumentet ?? []));
    
    echo "Email i projektit u dërgo me sukses!\n";
} catch (\Exception $e) {
    echo "Gabim gjatë dërgimit të emailit: " . $e->getMessage() . "\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}
