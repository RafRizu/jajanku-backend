<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // buyer
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('delivery_type', ['delivery', 'pickup'])->default('delivery');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'on_delivery', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
