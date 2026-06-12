<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ChildController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$user = User::where('role', 'nurse')->first();
if (!$user) {
    echo "No nurse user found\n";
    exit(1);
}

Auth::loginUsingId($user->id);

$request = Request::create('/children', 'POST', [
    'first_name' => 'TestChild',
    'middle_name' => 'Auto',
    'last_name' => 'Registrar',
    'date_of_birth' => date('Y-m-d', strtotime('-6 months')),
    'sex' => 'female',
    'gestational_age_weeks' => 38,
    'birth_weight' => 3.2,
    'birth_length' => 51.0,
    'birth_head_circumference' => 34.0,
    'mother_name' => 'Test Mother',
    'mother_phone' => '0712345678',
    'father_name' => 'Test Father',
    'father_phone' => '0712345679',
    'guardian_name' => 'Test Guardian',
    'guardian_phone' => '0712345680',
    'address' => '123 Test Ave',
    'location' => 'Test Location',
    'district' => 'Test District',
    'region' => 'Test Region',
    'medical_history' => 'No known history',
    'notes' => 'Created by automated registration test',
]);
$request->setUserResolver(fn () => $user);

$controller = new ChildController($app->make(App\Services\WHOGrowthService::class));
try {
    $response = $controller->store($request);
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "Registration request succeeded. Redirecting to: " . $response->getTargetUrl() . "\n";
    } else {
        echo "Registration request returned: " . get_class($response) . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . "\n";
    if (method_exists($e, 'errors')) {
        print_r($e->errors());
    }
    exit(1);
}
