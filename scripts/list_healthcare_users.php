<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$users = User::whereIn('role', ['admin', 'nurse', 'doctor'])->get();
foreach ($users as $user) {
    echo $user->id . ' ' . $user->email . ' ' . $user->role . PHP_EOL;
}
