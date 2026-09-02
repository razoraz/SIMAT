<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\View;
use App\Models\User;

$user = User::where('role', 'admin')->first() ?: User::first();
auth()->login($user);

try {
    $html = View::make('pages.form_astap', [
        'isEdit' => false,
        'editingAstap' => null,
        'jenisPengadaans' => [],
        'rekeningBelanjas' => [],
        'masterJenisAstap108' => []
    ])->render();
    echo "SUCCESS: form_astap rendered cleanly! Length: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
