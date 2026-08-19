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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('delivery_address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('driver_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('driver_longitude', 11, 8)->nullable()->after('driver_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'driver_latitude', 'driver_longitude']);
        });
    }
};
