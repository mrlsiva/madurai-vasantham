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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attandanceId')->startingValue(500001);
            $table->bigInteger('userId');
            $table->bigInteger('shiftId');
            $table->bigInteger('associatedId');
            $table->dateTime('startTime');
            $table->dateTime('endTime')->nullable();
            $table->date('startDate');
            $table->date('endDate')->nullable();
            $table->string('imageUrl')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('attendance');
    }
};
