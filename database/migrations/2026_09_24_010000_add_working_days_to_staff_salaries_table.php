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
        Schema::table('staff_salaries', function (Blueprint $table) {
            // Working days for this staff in this month only; null = the month's
            // working days from salary_working_days (same for all staff)
            $table->unsignedTinyInteger('working_days')->nullable()->after('monthly_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_salaries', function (Blueprint $table) {
            $table->dropColumn('working_days');
        });
    }
};
