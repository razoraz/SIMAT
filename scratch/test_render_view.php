<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$html = view('pages.data_astap', [
    'astaps' => [],
    'deletedAstaps' => [],
    'deletedNibars' => [],
    'dbMaster108' => []
])->render();

file_put_contents(__DIR__ . '/rendered_view.html', $html);
echo "Rendered view written! Total size: " . strlen($html) . " bytes\n";
