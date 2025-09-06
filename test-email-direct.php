<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Duke testuar dërgimin e emailit direkt...\n";
    
    $to = 'selver.kryeziu@gmail.com';
    $subject = 'Test Email nga ColiDecor';
    $message = 'Ky është një email testimi nga aplikacioni ColiDecor.';
    
    // Konfigurimi manual i SMTP
    config([
        'mail.mailer' => 'smtp',
        'mail.host' => 'smtp.gmail.com',
        'mail.port' => 587,
        'mail.username' => 'coli.deccor@gmail.com',
        'mail.password' => 'rpdz tcgo pwmn kyoq',
        'mail.encryption' => 'tls',
        'mail.from.address' => 'coli.deccor@gmail.com',
        'mail.from.name' => 'ColiDecor',
    ]);
    
    echo "Konfigurimi i SMTP:\n";
    echo "MAIL_MAILER: " . config('mail.mailer') . "\n";
    echo "MAIL_HOST: " . config('mail.host') . "\n";
    echo "MAIL_PORT: " . config('mail.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.username') . "\n";
    echo "MAIL_ENCRYPTION: " . config('mail.encryption') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";
    
    // Dërgo emailin duke përdorur API-në e Laravel
    \Illuminate\Support\Facades\Mail::raw($message, function ($email) use ($to, $subject) {
        $email->to($to)->subject($subject);
    });
    
    echo "Email u dërgua me sukses!\n";
} catch (\Exception $e) {
    echo "Gabim gjatë dërgimit të emailit: " . $e->getMessage() . "\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}
