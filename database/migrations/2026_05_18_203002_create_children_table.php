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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Parent/Guardian
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('unique_id')->unique(); // Hospital/clinic ID
            $table->date('date_of_birth');
            $table->enum('sex', ['male', 'female']);
            $table->integer('gestational_age_weeks')->nullable(); // For premature babies
            $table->string('birth_weight')->nullable(); // in kg
            $table->string('birth_length')->nullable(); // in cm
            $table->string('birth_head_circumference')->nullable(); // in cm
            $table->text('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->text('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->text('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('location')->nullable(); // Village/Suburb
            $table->string('district')->nullable();
            $table->string('region')->nullable();
            $table->text('medical_history')->nullable(); // Allergies, chronic conditions
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('user_id');
            $table->index('unique_id');
            $table->index('date_of_birth');
            $table->index('sex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};