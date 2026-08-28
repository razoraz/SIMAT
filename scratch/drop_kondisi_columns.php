<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (Schema::hasColumn('astap_mutasis', 'kondisi_sebelum')) {
    Schema::table('astap_mutasis', function (Blueprint $table) {
        $table->dropColumn(['kondisi_sebelum']);
    });
    echo "Dropped kondisi_sebelum column.\n";
}

if (Schema::hasColumn('astap_mutasis', 'kondisi_sesudah')) {
    Schema::table('astap_mutasis', function (Blueprint $table) {
        $table->dropColumn(['kondisi_sesudah']);
    });
    echo "Dropped kondisi_sesudah column.\n";
}

echo "Columns status in astap_mutasis:\n";
print_r(Schema::getColumnListing('astap_mutasis'));
