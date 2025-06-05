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
        Schema::create('shifttime', function (Blueprint $table) {
            $table->id('shiftId')->startingValue(20001);
            $table->string('shiftName');
            $table->time('startTime', $precision = 0);
            $table->time('endTime', $precision = 0);
            $table->string('status');
            $table->date('effectiveFrom');
            $table->date('effectiveTo');  
            $table->bigInteger('createdBy');
            $table->dateTime('createdOn');
            $table->bigInteger('modifiedBy')->nullable();
            $table->dateTime('modifiedOn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifttime');
    }
};
