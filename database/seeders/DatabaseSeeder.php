<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update hidden admin user with credentials: admin@test / admin@123
        User::updateOrCreate(
            ['email' => 'admin@test'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('admin@123'),
                'role' => 'admin',
                'location' => 'Head Office',
                'email_verified_at' => now(),
            ]
        );

        // Create or update test users with different roles (non-admin) - each with UNIQUE passwords
        User::updateOrCreate(
            ['email' => 'nurse@example.com'],
            [
                'name' => 'Nurse User',
                'password' => bcrypt('nurse@2024'),
                'role' => 'nurse',
                'facility_name' => 'City Health Center',
                'location' => 'City Center',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'doctor@example.com'],
            [
                'name' => 'Doctor User',
                'password' => bcrypt('doctor@2024'),
                'role' => 'doctor',
                'facility_name' => 'Regional Hospital',
                'location' => 'Regional Area',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'parent@example.com'],
            [
                'name' => 'Parent User',
                'password' => bcrypt('parent@2024'),
                'role' => 'parent',
                'location' => 'Downtown',
                'email_verified_at' => now(),
            ]
        );

        // Seed immunization schedules
        $this->call([
            ImmunizationScheduleSeeder::class,
            WHOGrowthStandardSeeder::class,
        ]);
    }
}