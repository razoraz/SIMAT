<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

echo "Testing GET /master-data/hibah:\n";
$req = \Illuminate\Http\Request::create('/master-data/hibah', 'GET');
$res = $app->handle($req);
echo "Status: " . $res->getStatusCode() . "\n";

echo "Testing GET /astap/pilih-jenis:\n";
$req2 = \Illuminate\Http\Request::create('/astap/pilih-jenis', 'GET');
$res2 = $app->handle($req2);
echo "Status: " . $res2->getStatusCode() . "\n";

echo "Testing GET /astap/create-hibah:\n";
$req3 = \Illuminate\Http\Request::create('/astap/create-hibah', 'GET');
$res3 = $app->handle($req3);
echo "Status: " . $res3->getStatusCode() . "\n";

echo "Testing GET /astap (Katalog Data ASTAP):\n";
$req4 = \Illuminate\Http\Request::create('/astap', 'GET');
$res4 = $app->handle($req4);
echo "Status: " . $res4->getStatusCode() . "\n";
