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
        Schema::create('chitfund_users', function (Blueprint $table) {
            $table->id('user_id')->startingValue(1001);
            $table->string('user_name', 100);
			$table->string('mobile_no', 20);
			$table->string('address');
			$table->bigInteger('plan_id');
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
        Schema::dropIfExists('chitfund_users');
    }
};
