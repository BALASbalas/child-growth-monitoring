<?php

namespace Database\Seeders;

use App\Models\ImmunizationSchedule;
use Illuminate\Database\Seeder;

class ImmunizationScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = ImmunizationSchedule::getStandardSchedules();

        foreach ($schedules as $schedule) {
            ImmunizationSchedule::firstOrCreate(
                ['name' => $schedule['name']],
                $schedule
            );
        }
    }
}