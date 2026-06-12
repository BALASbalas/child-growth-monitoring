<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\GrowthMeasurementController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ChildApiController;
use App\Http\Controllers\Api\GrowthMeasurementApiController;
use App\Http\Controllers\Api\ImmunizationApiController;
use Illuminate\Support\Facades\Route;

// ============================================
// Public Routes
// ============================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================
// Fallback Dashboard (redirects based on role)
// ============================================
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'nurse' => redirect()->route('nurse.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        'parent' => redirect()->route('parent.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ============================================
// Role-Specific Dashboard Routes
// ============================================

// Admin Dashboard (admin only)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

// Nurse Dashboard (nurse only)
Route::get('/nurse/dashboard', function () {
    return view('nurse.dashboard');
})->middleware(['auth', 'verified', 'role:nurse'])->name('nurse.dashboard');

// Doctor Dashboard (doctor only)
Route::get('/doctor/dashboard', function () {
    return view('doctor.dashboard');
})->middleware(['auth', 'verified', 'role:doctor'])->name('doctor.dashboard');

// Parent Dashboard (parent only)
Route::get('/parent/dashboard', function () {
    return view('parent-dashboard.dashboard');
})->middleware(['auth', 'verified', 'role:parent'])->name('parent.dashboard');

// ============================================
// API Routes (AJAX endpoints)
// ============================================
Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    // Users API (admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserApiController::class, 'index'])->name('api.users.index');
        Route::post('/users', [UserApiController::class, 'store'])->name('api.users.store');
        Route::get('/users/{user}', [UserApiController::class, 'show'])->name('api.users.show');
        Route::put('/users/{user}', [UserApiController::class, 'update'])->name('api.users.update');
        Route::patch('/users/{user}/toggle-status', [UserApiController::class, 'toggleStatus'])->name('api.users.toggle-status');
        Route::delete('/users/{user}', [UserApiController::class, 'destroy'])->name('api.users.destroy');
    });

    // Children API
    Route::get('/children', [ChildApiController::class, 'index'])->name('api.children.index');
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::post('/children', [ChildApiController::class, 'store'])->name('api.children.store');
        Route::get('/children/{child}', [ChildApiController::class, 'show'])->name('api.children.show');
        Route::put('/children/{child}', [ChildApiController::class, 'update'])->name('api.children.update');
        Route::patch('/children/{child}/toggle-status', [ChildApiController::class, 'toggleStatus'])->name('api.children.toggle-status');
        Route::delete('/children/{child}', [ChildApiController::class, 'destroy'])->name('api.children.destroy');
    });

    // Growth Measurements API (admin, nurse, doctor)
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::get('/growth-measurements', [GrowthMeasurementApiController::class, 'index'])->name('api.growth-measurements.index');
        Route::post('/growth-measurements', [GrowthMeasurementApiController::class, 'store'])->name('api.growth-measurements.store');
        Route::get('/growth-measurements/{growthMeasurement}', [GrowthMeasurementApiController::class, 'show'])->name('api.growth-measurements.show');
        Route::put('/growth-measurements/{growthMeasurement}', [GrowthMeasurementApiController::class, 'update'])->name('api.growth-measurements.update');
        Route::delete('/growth-measurements/{growthMeasurement}', [GrowthMeasurementApiController::class, 'destroy'])->name('api.growth-measurements.destroy');
    });

    // Immunizations API (admin, nurse, doctor)
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::get('/immunizations', [ImmunizationApiController::class, 'index'])->name('api.immunizations.index');
        Route::post('/immunizations', [ImmunizationApiController::class, 'store'])->name('api.immunizations.store');
        Route::get('/immunizations/{immunization}', [ImmunizationApiController::class, 'show'])->name('api.immunizations.show');
        Route::put('/immunizations/{immunization}', [ImmunizationApiController::class, 'update'])->name('api.immunizations.update');
        Route::delete('/immunizations/{immunization}', [ImmunizationApiController::class, 'destroy'])->name('api.immunizations.destroy');
    });

    // Devices API (admin, nurse, doctor)
    Route::middleware(['role:admin,nurse,doctor'])->prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'apiIndex'])->name('api.devices.index');
    });
});

// ============================================
// Authenticated Routes with Role Protection
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Profile routes (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ============================================
    // Children Routes
    // Cross-role visibility: admin, doctor, nurse can see ALL children
    // Parents/guardians see only their linked children
    // ============================================
    
    // View children - all authenticated users
    Route::get('children', [ChildController::class, 'index'])->name('children.index');
    
    // Create children - admin, nurse, doctor
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::get('children/create', [ChildController::class, 'create'])->name('children.create');
        Route::post('children', [ChildController::class, 'store'])->name('children.store');
        Route::get('children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
        Route::put('children/{child}', [ChildController::class, 'update'])->name('children.update');
        Route::delete('children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');
    });
    
    // Show child - all authenticated users (cross-role visibility enabled)
    Route::get('children/{child}', [ChildController::class, 'show'])->name('children.show');
    
    // Growth chart & immunizations - viewable by all (cross-role visibility)
    Route::get('children/{child}/growth-chart', [ChildController::class, 'growthChart'])->name('children.growth-chart');
    Route::get('children/{child}/immunizations', [ChildController::class, 'immunizations'])->name('children.immunizations');

    // Parent: print child health details
    Route::get('children/{child}/print', [ChildController::class, 'printDetails'])->name('children.print');

    // ============================================
    // Growth Measurements (admin, nurse, doctor - cross-role visibility)
    // ============================================
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::resource('growth-measurements', GrowthMeasurementController::class);
        Route::post('growth-measurements/child/{child}', [GrowthMeasurementController::class, 'storeForChild'])->name('growth-measurements.store-for-child');
        Route::get('growth-measurements/child/{child}', [GrowthMeasurementController::class, 'indexForChild'])->name('growth-measurements.index-for-child');
    });

    // ============================================
    // Immunizations (admin, nurse, doctor)
    // ============================================
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::resource('immunizations', ImmunizationController::class);
        Route::get('immunizations/schedule/generate/{child}', [ImmunizationController::class, 'generateSchedule'])->name('immunizations.generate-schedule');
        Route::patch('immunizations/{immunization}/administer', [ImmunizationController::class, 'administer'])->name('immunizations.administer');
        Route::get('immunizations/upcoming', [ImmunizationController::class, 'upcoming'])->name('immunizations.upcoming');
        Route::get('immunizations/overdue', [ImmunizationController::class, 'overdue'])->name('immunizations.overdue');
    });

    // ============================================
    // Devices (admin + nurse + doctor)
    // ============================================
    Route::middleware(['role:admin,nurse,doctor'])->group(function () {
        Route::resource('devices', DeviceController::class);
        Route::post('devices/{device}/connect', [DeviceController::class, 'connect'])->name('devices.connect');
        Route::post('devices/{device}/disconnect', [DeviceController::class, 'disconnect'])->name('devices.disconnect');
        Route::post('devices/{device}/calibrate', [DeviceController::class, 'calibrate'])->name('devices.calibrate');
    });

    // ============================================
    // Reports (all authenticated users)
    // Admin can generate all reports + statistics
    // ============================================
    Route::get('reports/growth', [ReportController::class, 'growthReport'])->name('reports.growth');
    Route::get('reports/immunization', [ReportController::class, 'immunizationReport'])->name('reports.immunization');
    Route::get('reports/export/{child}', [ReportController::class, 'exportChildReport'])->name('reports.export-child');
    Route::get('reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');

    // Admin: system-wide reports and management
    Route::middleware(['role:admin'])->group(function () {
        Route::get('admin/users', [ReportController::class, 'usersReport'])->name('admin.users');
        Route::patch('admin/users/{user}/toggle-status', [ReportController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        Route::delete('admin/users/{user}', [ReportController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::get('admin/system-report', [ReportController::class, 'systemReport'])->name('admin.system-report');
        Route::get('admin/export-all', [ReportController::class, 'exportAll'])->name('admin.export-all');
        Route::get('admin/settings', function () { return view('admin.settings'); })->name('admin.settings');
        Route::post('admin/settings', function () { return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.'); });
        Route::get('admin/backup', function () { return view('admin.backup'); })->name('admin.backup');
        Route::post('admin/backup/create', function () { return redirect()->route('admin.backup')->with('success', 'Backup created successfully.'); });
        Route::post('admin/backup/restore', function () { return redirect()->route('admin.backup')->with('success', 'Backup restored successfully.'); });
        Route::get('admin/audit-logs', function () { return view('admin.audit-logs'); })->name('admin.audit-logs');

        // Settings API endpoints
        Route::get('admin/api/settings', [AdminSettingController::class, 'getSettings'])->name('admin.api.settings');
        Route::post('admin/api/settings', [AdminSettingController::class, 'saveSettings'])->name('admin.api.settings.save');
        Route::post('admin/api/settings/logo', [AdminSettingController::class, 'uploadLogo'])->name('admin.api.settings.logo');

        // Backup API endpoints
        Route::get('admin/api/backups', [AdminSettingController::class, 'getBackups'])->name('admin.api.backups');
        Route::post('admin/api/backups/create', [AdminSettingController::class, 'createBackup'])->name('admin.api.backups.create');
        Route::post('admin/api/backups/restore', [AdminSettingController::class, 'restoreBackup'])->name('admin.api.backups.restore');
        Route::get('admin/api/backups/{backup}/download', [AdminSettingController::class, 'downloadBackup'])->name('admin.api.backups.download');
        Route::delete('admin/api/backups/{backup}', [AdminSettingController::class, 'deleteBackup'])->name('admin.api.backups.delete');

        // Audit Log API endpoints
        Route::get('admin/api/audit-logs', [AdminSettingController::class, 'getAuditLogs'])->name('admin.api.audit-logs');
        Route::get('admin/api/audit-log-actions', [AdminSettingController::class, 'getAuditLogActions'])->name('admin.api.audit-log-actions');
    });
});

// ============================================
// Authentication Routes
// ============================================
require __DIR__.'/auth.php';