<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::with('unitModel')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | UnitID: {$u->unit_id} | Unit (string): '{$u->unit}' | Kepala: " . ($u->unitModel ? $u->unitModel->kepala : 'NULL') . "\n";
}
