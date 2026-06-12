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
        Schema::create('device_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name'); // e.g., "Digital Scale 01"
            $table->string('device_type'); // "weight_scale", "height_rod", "muac_tape"
            $table->string('serial_number')->unique();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('connection_type')->default('serial'); // serial, bluetooth, usb
            $table->string('com_port')->nullable(); // e.g., "COM3"
            $table->integer('baud_rate')->default(9600);
            $table->integer('data_bits')->default(8);
            $table->string('parity')->default('none'); // none, even, odd
            $table->integer('stop_bits')->default(1);
            $table->string('data_format')->nullable(); // e.g., "N,8,1" for serial config
            $table->text('calibration_data')->nullable(); // JSON calibration settings
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('device_type');
            $table->index('serial_number');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_connections');
    }
};