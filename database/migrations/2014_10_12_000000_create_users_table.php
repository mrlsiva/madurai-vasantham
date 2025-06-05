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
        Schema::create('users', function (Blueprint $table) {           
            $table->id('userId')->startingValue(100001);
			$table->string('empId')->unique();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('mobile');                 
            $table->char('gender');
            $table->date('DOB');
            $table->string('status');
            $table->integer('role');
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
        Schema::dropIfExists('users');
    }
};
