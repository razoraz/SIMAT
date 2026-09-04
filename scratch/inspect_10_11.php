<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a10 = \App\Models\Astap::with('registers', 'jenisAstap')->find(10);
$a11 = \App\Models\Astap::with('registers', 'jenisAstap')->find(11);

echo "=== ASTAP 10 ===\n";
print_r($a10->toArray());

echo "\n=== ASTAP 11 ===\n";
print_r($a11->toArray());
