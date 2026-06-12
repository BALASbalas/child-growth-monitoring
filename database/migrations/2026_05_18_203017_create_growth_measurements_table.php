<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('growth_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Healthcare worker who took measurement
            $table->date('measurement_date');
            $table->decimal('weight', 5, 3)->nullable(); // in kg (e.g., 12.500)
            $table->decimal('height', 5, 2)->nullable(); // in cm (e.g., 85.50)
            $table->decimal('head_circumference', 5, 2)->nullable(); // in cm
            $table->decimal('mid_upper_arm_circumference', 5, 2)->nullable(); // MUAC in cm
            $table->decimal('temperature', 4, 1)->nullable(); // in Celsius
            $table->integer('age_in_months')->nullable(); // Calculated age at measurement
            $table->decimal('weight_for_age_zscore', 5, 2)->nullable(); // WAZ
            $table->decimal('height_for_age_zscore', 5, 2)->nullable(); // HAZ
            $table->decimal('weight_for_height_zscore', 5, 2)->nullable(); // WHZ
            $table->decimal('bmi', 5, 2)->nullable(); // Body Mass Index
            $table->decimal('bmi_for_age_zscore', 5, 2)->nullable(); // BMI-for-age
            $table->enum('nutritional_status', ['severe_underweight', 'moderate_underweight', 'normal', 'overweight', 'obese'])->nullable();
            $table->enum('stunting_status', ['severe', 'moderate', 'normal'])->nullable();
            $table->enum('wasting_status', ['severe', 'moderate', 'normal'])->nullable();
            $table->text('clinical_notes')->nullable();
            $table->boolean('is_from_device')->default(false); // Whether measurement came from digital device
            $table->string('device_id')->nullable(); // Device serial/identifier
            $table->timestamps();
            
            // Indexes
            $table->index('child_id');
            $table->index('user_id');
            $table->index('measurement_date');
            $table->index('age_in_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_measurements');
    }
};