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
        Schema::create('shifttimewithusers', function (Blueprint $table) {          
            $table->id('associatedId')->startingValue(30001);
            $table->bigInteger('userId');
            $table->bigInteger('shiftId');
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
        Schema::dropIfExists('shifttimewithusers');
    }
};
