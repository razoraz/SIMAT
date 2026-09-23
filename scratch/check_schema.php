<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$colsReg = \Illuminate\Support\Facades\Schema::getColumnListing('astap_registers');
echo "astap_registers cols: " . implode(', ', $colsReg) . "\n";
$colsAstap = \Illuminate\Support\Facades\Schema::getColumnListing('astaps');
echo "astaps cols: " . implode(', ', $colsAstap) . "\n";
