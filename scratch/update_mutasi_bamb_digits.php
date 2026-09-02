<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AstapMutasi;

$mutasis = AstapMutasi::all();
echo "Found " . $mutasis->count() . " mutasi records.\n";

foreach ($mutasis as $m) {
    $oldBamb = $m->nomor_bamb;
    if (preg_match('/^MTS-(\d{4})-(\d+)$/', $oldBamb, $matches)) {
        $year = $matches[1];
        $seqInt = intval($matches[2]);
        $newBamb = 'MTS-' . $year . '-' . str_pad($seqInt, 3, '0', STR_PAD_LEFT);
        if ($oldBamb !== $newBamb) {
            $m->update(['nomor_bamb' => $newBamb]);
            echo "Updated ID {$m->id}: {$oldBamb} -> {$newBamb}\n";
        } else {
            echo "ID {$m->id} already correct: {$oldBamb}\n";
        }
    }
}
echo "Done!\n";
