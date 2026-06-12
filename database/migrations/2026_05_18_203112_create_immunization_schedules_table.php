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
        Schema::create('immunization_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "BCG at birth", "Pentavalent 1 at 6 weeks"
            $table->string('vaccine_name'); // BCG, OPV, Pentavalent, etc.
            $table->string('vaccine_type')->nullable(); // e.g., "Pentavalent", "IPV"
            $table->integer('due_age_weeks')->nullable(); // Age in weeks when due
            $table->integer('due_age_months')->nullable(); // Age in months when due
            $table->integer('priority_order')->default(1); // Order of administration
            $table->string('route')->nullable(); // IM, SC, Oral
            $table->decimal('dose_volume', 10, 2)->nullable(); // in ml (or IU)
            $table->text('description')->nullable();
            $table->text('contraindications')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('vaccine_name');
            $table->index('due_age_weeks');
            $table->index('due_age_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_schedules');
    }
};