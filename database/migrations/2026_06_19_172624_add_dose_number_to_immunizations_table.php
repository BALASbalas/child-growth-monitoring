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
        Schema::table('immunizations', function (Blueprint $table) {
            if (!Schema::hasColumn('immunizations', 'dose_number')) {
                $table->integer('dose_number')->nullable()->after('vaccine_type');
            }
            if (!Schema::hasColumn('immunizations', 'administered_by')) {
                $table->string('administered_by')->nullable()->after('health_worker_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immunizations', function (Blueprint $table) {
            if (Schema::hasColumn('immunizations', 'dose_number')) {
                $table->dropColumn('dose_number');
            }
            if (Schema::hasColumn('immunizations', 'administered_by')) {
                $table->dropColumn('administered_by');
            }
        });
    }
};