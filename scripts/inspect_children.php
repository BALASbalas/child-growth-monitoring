<?php
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Child;

echo "TOTAL: " . Child::count() . PHP_EOL;
echo "ACTIVE: " . Child::active()->count() . PHP_EOL;
$all = Child::active()->limit(5)->get(['id','first_name','last_name','unique_id','is_active'])->toArray();
print_r($all);
