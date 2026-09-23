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
        // Monthly salary per staff. A new row with a later effective_from is an
        // increment; older months keep calculating with the salary valid then.
        Schema::create('staff_salaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userId');
            $table->decimal('monthly_salary', 10, 2);
            $table->date('effective_from'); // always the 1st of a month
            $table->timestamps();

            $table->unique(['userId', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salaries');
    }
};
