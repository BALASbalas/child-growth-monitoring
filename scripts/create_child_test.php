<?php
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Child;
use Illuminate\Support\Str;

echo "Before: " . Child::count() . PHP_EOL;

$child = Child::create([
    'user_id' => 1,
    'first_name' => 'Testy',
    'middle_name' => 'Auto',
    'last_name' => 'Insert',
    'unique_id' => 'CHD-TEST-' . strtoupper(Str::random(6)),
    'date_of_birth' => now()->subMonths(6)->format('Y-m-d'),
    'sex' => 'male',
    'is_active' => true,
]);

echo "Inserted ID: " . $child->id . PHP_EOL;
echo "After: " . Child::count() . PHP_EOL;
print_r($child->toArray());
