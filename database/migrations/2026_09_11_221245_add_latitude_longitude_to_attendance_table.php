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
            // punch-in location
            $table->decimal('latitude', 10, 7)->nullable()->after('imageUrl');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            // punch-out location (separate columns so checking out doesn't overwrite where they checked in)
            $table->decimal('out_latitude', 10, 7)->nullable()->after('longitude');
            $table->decimal('out_longitude', 10, 7)->nullable()->after('out_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'out_latitude', 'out_longitude']);
        });
    }
};
