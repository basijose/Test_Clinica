<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

echo "--- Settings Table Content ---\n";
$allSettings = Setting::all();
echo "Total Records: " . $allSettings->count() . "\n";

if ($allSettings->isEmpty()) {
    echo "WARNING: Table is empty! Did you run the seeder?\n";
} else {
    foreach ($allSettings as $s) {
        echo "[{$s->group}] {$s->key} = {$s->value}\n";
    }
}

echo "\n--- Controller Response Structure ---\n";
$grouped = $allSettings->groupBy('group');
echo json_encode($grouped, JSON_PRETTY_PRINT);
