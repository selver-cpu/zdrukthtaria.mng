<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Duke testuar dërgimin e emailit me konfigurim të përkohshëm...\n";
    
    $to = 'selver.kryeziu@gmail.com';
    $subject = 'Test Email nga ColiDecor';
    $message = 'Ky është një email testimi nga aplikacioni ColiDecor.';
    
    // Përdor konfigurimin e përkohshëm
    $config = require __DIR__.'/config/mail-custom.php';
    
    echo "Konfigurimi i SMTP:\n";
    echo "MAIL_MAILER: " . $config['mailer'] . "\n";
    echo "MAIL_HOST: " . $config['host'] . "\n";
    echo "MAIL_PORT: " . $config['port'] . "\n";
    echo "MAIL_USERNAME: " . $config['username'] . "\n";
    echo "MAIL_ENCRYPTION: " . $config['encryption'] . "\n";
    echo "MAIL_FROM_ADDRESS: " . $config['from']['address'] . "\n";
    echo "MAIL_FROM_NAME: " . $config['from']['name'] . "\n\n";
    
    // Krijo një instancë të re të Swift Mailer
    $transport = new Swift_SmtpTransport($config['host'], $config['port'], $config['encryption']);
    $transport->setUsername($config['username']);
    $transport->setPassword($config['password']);
    
    $mailer = new Swift_Mailer($transport);
    
    // Krijo mesazhin
    $swiftMessage = new Swift_Message($subject);
    $swiftMessage->setFrom([$config['from']['address'] => $config['from']['name']]);
    $swiftMessage->setTo([$to]);
    $swiftMessage->setBody($message);
    
    // Dërgo emailin
    $result = $mailer->send($swiftMessage);
    
    if ($result) {
        echo "Email u dërgua me sukses!\n";
    } else {
        echo "Dërgimi i emailit dështoi.\n";
    }
} catch (\Exception $e) {
    echo "Gabim gjatë dërgimit të emailit: " . $e->getMessage() . "\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}
