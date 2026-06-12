<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('who_growth_standards', function (Blueprint $table) {
            $table->decimal('head_circumference_minus_1sd', 5, 2)
                ->nullable()
                ->after('head_circumference_minus_2sd');
            $table->decimal('head_circumference_plus_1sd', 5, 2)
                ->nullable()
                ->after('head_circumference_minus_1sd');
        });
    }

    public function down(): void
    {
        Schema::table('who_growth_standards', function (Blueprint $table) {
            $table->dropColumn(['head_circumference_minus_1sd', 'head_circumference_plus_1sd']);
        });
    }
};
