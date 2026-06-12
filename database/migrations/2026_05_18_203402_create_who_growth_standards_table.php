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
        Schema::create('who_growth_standards', function (Blueprint $table) {
            $table->id();
            $table->enum('sex', ['male', 'female']);
            $table->integer('age_in_months'); // 0 to 60 months
            $table->integer('age_in_days')->nullable(); // For more precise calculations
            $table->decimal('weight_median', 5, 3); // Median weight in kg
            $table->decimal('weight_l', 5, 3); // Box-Cox power parameter
            $table->decimal('weight_m', 5, 3); // Median
            $table->decimal('weight_s', 5, 3); // Coefficient of variation
            $table->decimal('weight_minus_3sd', 5, 3); // -3 SD
            $table->decimal('weight_minus_2sd', 5, 3); // -2 SD
            $table->decimal('weight_minus_1sd', 5, 3); // -1 SD
            $table->decimal('weight_plus_1sd', 5, 3); // +1 SD
            $table->decimal('weight_plus_2sd', 5, 3); // +2 SD
            $table->decimal('weight_plus_3sd', 5, 3); // +3 SD
            
            $table->decimal('height_median', 5, 2); // Median height in cm
            $table->decimal('height_l', 5, 3);
            $table->decimal('height_m', 5, 3);
            $table->decimal('height_s', 5, 3);
            $table->decimal('height_minus_3sd', 5, 2);
            $table->decimal('height_minus_2sd', 5, 2);
            $table->decimal('height_minus_1sd', 5, 2);
            $table->decimal('height_plus_1sd', 5, 2);
            $table->decimal('height_plus_2sd', 5, 2);
            $table->decimal('height_plus_3sd', 5, 2);
            
            $table->decimal('bmi_median', 5, 2); // Median BMI
            $table->decimal('bmi_l', 5, 3);
            $table->decimal('bmi_m', 5, 3);
            $table->decimal('bmi_s', 5, 3);
            $table->decimal('bmi_minus_3sd', 5, 2);
            $table->decimal('bmi_minus_2sd', 5, 2);
            $table->decimal('bmi_minus_1sd', 5, 2);
            $table->decimal('bmi_plus_1sd', 5, 2);
            $table->decimal('bmi_plus_2sd', 5, 2);
            $table->decimal('bmi_plus_3sd', 5, 2);
            
            $table->decimal('head_circumference_median', 5, 2);
            $table->decimal('head_circumference_l', 5, 3);
            $table->decimal('head_circumference_m', 5, 3);
            $table->decimal('head_circumference_s', 5, 3);
            $table->decimal('head_circumference_minus_3sd', 5, 2);
            $table->decimal('head_circumference_minus_2sd', 5, 2);
            $table->decimal('head_circumference_plus_2sd', 5, 2);
            $table->decimal('head_circumference_plus_3sd', 5, 2);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['sex', 'age_in_months']);
            $table->index('sex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('who_growth_standards');
    }
};