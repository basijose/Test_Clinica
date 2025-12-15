<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "--- Configuración Detectada ---\n";
echo "Mailer: " . Config::get('mail.default') . "\n";
echo "Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "Username: " . Config::get('mail.mailers.smtp.username') . "\n";
echo "Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "From: " . Config::get('mail.from.address') . "\n";

try {
    echo "\nIntentando enviar email de prueba a bot@hilet.com...\n";
    
    Mail::raw('Si lees esto, el envío de emails funciona correctamente.', function ($message) {
        $message->to('bot@hilet.com')
                ->subject('Prueba de SMTP - Panel CNF');
    });
    
    echo "✅ ¡Email enviado con éxito! Revisa la bandeja de entrada.\n";
} catch (\Throwable $e) {
    echo "❌ ERROR al enviar email:\n";
    echo $e->getMessage() . "\n";
}
