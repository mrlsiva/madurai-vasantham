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
        // Working days per month, the same for all staff (e.g. Sep 2026 = 26).
        Schema::create('salary_working_days', function (Blueprint $table) {
            $table->id();
            $table->date('month')->unique(); // always the 1st of the month
            $table->unsignedTinyInteger('working_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_working_days');
    }
};
