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
            $table->index('status');
            $table->index('user_id');
            $table->index('shop_id');
            $table->index('driver_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('shop_id');
            $table->index('category_id');
            $table->index('is_available');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('status');
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['shop_id']);
            $table->dropIndex(['driver_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['shop_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_available']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });
    }
};
