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
        Schema::create('chitfund_scheme', function (Blueprint $table) {
            $table->id('plan_id')->startingValue(1001);
            $table->string('plan_name');
			$table->string('plan_amount');  
            $table->date('start_date');
            $table->date('end_date');  
			$table->boolean('is_active')->default(1);        
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
        Schema::dropIfExists('chitfund_scheme');
    }
};
