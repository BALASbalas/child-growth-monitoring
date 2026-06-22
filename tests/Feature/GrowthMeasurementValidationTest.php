<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrowthMeasurementValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_measurement_requires_at_least_one_numeric_value_before_saving(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $child = Child::create([
            'user_id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'unique_id' => 'CH-001',
            'date_of_birth' => '2023-01-01',
            'sex' => 'female',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/growth-measurements', [
                'child_id' => $child->id,
                'measurement_date' => now()->toDateString(),
                'clinical_notes' => 'Only notes provided',
            ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).',
            ]);
    }
}
