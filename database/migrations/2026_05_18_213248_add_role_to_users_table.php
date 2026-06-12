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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'nurse', 'doctor', 'guardian', 'parent'])->default('parent')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('facility_name')->nullable()->after('phone'); // For healthcare workers
            $table->string('license_number')->nullable()->after('facility_name'); // For nurses
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'facility_name', 'license_number']);
        });
    }
};