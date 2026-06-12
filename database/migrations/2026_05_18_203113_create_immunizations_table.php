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
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Healthcare worker
            $table->foreignId('immunization_schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('vaccine_name'); // BCG, OPV, DPT, etc.
            $table->string('vaccine_type')->nullable(); // e.g., "Pentavalent", "IPV"
            $table->string('batch_number')->nullable();
            $table->date('date_administered');
            $table->date('next_due_date')->nullable();
            $table->enum('status', ['scheduled', 'administered', 'missed', 'cancelled'])->default('scheduled');
            $table->string('site')->nullable(); // Injection site (e.g., left thigh, right arm)
            $table->string('route')->nullable(); // Route of administration (IM, SC, Oral)
            $table->decimal('dose_volume', 10, 2)->nullable(); // in ml (or IU)
            $table->text('adverse_reactions')->nullable();
            $table->text('notes')->nullable();
            $table->string('health_facility')->nullable();
            $table->string('health_worker_name')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('child_id');
            $table->index('immunization_schedule_id');
            $table->index('status');
            $table->index('date_administered');
            $table->index('next_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};