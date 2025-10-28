<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Duke testuar dërgimin e emailit...\n";
    
    $to = 'selver.kryeziu@gmail.com';
    $subject = 'Test Email nga ColiDecor';
    $message = 'Ky është një email testimi nga aplikacioni ColiDecor.';
    
    echo "Konfigurimi i SMTP:\n";
    echo "MAIL_MAILER: " . config('mail.mailer') . "\n";
    echo "MAIL_HOST: " . config('mail.host') . "\n";
    echo "MAIL_PORT: " . config('mail.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.username') . "\n";
    echo "MAIL_ENCRYPTION: " . config('mail.encryption') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";
    
    \Illuminate\Support\Facades\Mail::raw($message, function ($email) use ($to, $subject) {
        $email->to($to)->subject($subject);
    });
    
    echo "Email u dërgua me sukses!\n";
} catch (\Exception $e) {
    echo "Gabim gjatë dërgimit të emailit: " . $e->getMessage() . "\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}
