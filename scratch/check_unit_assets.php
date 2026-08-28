<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Total Registers: " . App\Models\AstapRegister::count() . "\n";
echo "Registers with unit_id: " . App\Models\AstapRegister::whereNotNull('unit_id')->count() . "\n";
echo "Registers with ruang_pemegang: " . App\Models\AstapRegister::whereNotNull('ruang_pemegang')->count() . "\n\n";

foreach (App\Models\Unit::take(10)->get() as $u) {
    $cnt = App\Models\AstapRegister::where('unit_id', $u->id)
        ->orWhere(function($q) use ($u) {
            $q->whereNotNull('ruang_pemegang')
              ->where('ruang_pemegang', 'LIKE', '%' . $u->nama . '%');
        })->count();
    echo $u->kode_unit . ' - ' . $u->nama . ': ' . $cnt . " items\n";
}
