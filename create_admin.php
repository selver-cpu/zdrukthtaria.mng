<?php

// Ky skript krijon një përdorues admin në bazën e të dhënave

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Rolet;
use Illuminate\Support\Facades\Hash;

// Gjej rolin administrator ose krijo nëse nuk ekziston
$rol = Rolet::firstOrCreate(
    ['emri_rolit' => 'administrator'],
    ['emri_rolit' => 'administrator']
);

echo "Roli administrator u krijua me ID: " . $rol->rol_id . "\n";

// Krijo përdoruesin admin
$user = User::updateOrCreate(
    ['email' => 'selver.kryeziu@gmail.com'],
    [
        'rol_id' => $rol->rol_id,
        'emri' => 'Selver',
        'mbiemri' => 'Kryeziu',
        'fjalekalimi_hash' => Hash::make('Veli2024@'),
        'aktiv' => 1,
    ]
);

echo "Përdoruesi admin u krijua me ID: " . $user->perdorues_id . "\n";
echo "Email: " . $user->email . "\n";
echo "Fjalëkalimi: Veli2024@\n";

// Shfaq të gjithë përdoruesit në bazën e të dhënave
$users = User::all();
echo "\nLista e përdoruesve në bazën e të dhënave:\n";
foreach ($users as $u) {
    echo "ID: " . $u->perdorues_id . ", Emri: " . $u->emri . " " . $u->mbiemri . ", Email: " . $u->email . "\n";
}
