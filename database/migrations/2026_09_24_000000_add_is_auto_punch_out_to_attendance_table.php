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
        Schema::table('attendance', function (Blueprint $table) {
            // 1 = endTime was set by the attendance:auto-punch-out command at shift end,
            // because the staff did not punch out before midnight
            $table->boolean('is_auto_punch_out')->default(0)->after('endDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('is_auto_punch_out');
        });
    }
};
