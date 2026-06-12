<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            $settings = SystemSetting::pluck('value', 'key')->toArray();
            $settingsObj = (object) $settings;
            
            // Make settings available globally in all views
            View::share('systemSettings', $settingsObj);
            
            // Also set config values
            if (isset($settings['system_name'])) {
                config(['app.name' => $settings['system_name']]);
            }
        } catch (\Exception $e) {
            // Table might not exist yet (migrations not run)
            View::share('systemSettings', (object) [
                'system_name' => 'Child Growth Monitoring System',
                'system_logo' => null,
                'system_email' => '',
                'footer_text' => '',
                'phone_number' => '',
                'address' => '',
            ]);
        }
    }
}