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
        Schema::create('chitfund_luckydraw', function (Blueprint $table) {
            $table->id('draw_id')->startingValue(1001);
			$table->bigInteger('user_id');
			$table->bigInteger('plan_id');			 
			$table->dateTime('draw_date')->nullable();
			$table->boolean('draw_status')->default(0);			
            $table->dateTime('createdOn')->nullable();
            $table->dateTime('modifiedOn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitfund_luckydraw');
    }
};
