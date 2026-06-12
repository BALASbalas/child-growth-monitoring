<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, file
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Insert default settings with all required fields
        $settings = [
            ['key' => 'system_name', 'value' => 'Child Growth Monitoring System', 'type' => 'string', 'group' => 'general', 'label' => 'System Name'],
            ['key' => 'system_email', 'value' => 'admin@childgrowth.local', 'type' => 'string', 'group' => 'general', 'label' => 'Contact Email'],
            ['key' => 'system_logo', 'value' => null, 'type' => 'file', 'group' => 'general', 'label' => 'System Logo'],
            ['key' => 'footer_text', 'value' => '© ' . date('Y') . ' Child Growth Monitoring System. All rights reserved.', 'type' => 'string', 'group' => 'general', 'label' => 'Footer Text'],
            ['key' => 'phone_number', 'value' => '+255 123 456 789', 'type' => 'string', 'group' => 'general', 'label' => 'Phone Number'],
            ['key' => 'address', 'value' => 'Dar es Salaam, Tanzania', 'type' => 'string', 'group' => 'general', 'label' => 'Address'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general', 'label' => 'Timezone'],
            ['key' => 'language', 'value' => 'en', 'type' => 'string', 'group' => 'general', 'label' => 'Language'],
            ['key' => 'per_page', 'value' => '15', 'type' => 'integer', 'group' => 'general', 'label' => 'Records Per Page'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'general', 'label' => 'Date Format'],
            ['key' => 'time_format', 'value' => 'H:i:s', 'type' => 'string', 'group' => 'general', 'label' => 'Time Format'],
            ['key' => 'auto_backup_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'backup', 'label' => 'Auto Backup Enabled'],
            ['key' => 'backup_retention_days', 'value' => '30', 'type' => 'integer', 'group' => 'backup', 'label' => 'Backup Retention Days'],
            ['key' => 'audit_log_retention_days', 'value' => '365', 'type' => 'integer', 'group' => 'general', 'label' => 'Audit Log Retention Days'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'label' => 'Maintenance Mode'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};