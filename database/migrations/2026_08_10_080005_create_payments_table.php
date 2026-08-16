<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['transfer', 'cash', 'midtrans', 'qris'])->default('cash');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('snap_token')->nullable(); // for Midtrans
            $table->string('proof_image')->nullable(); // for transfer proof
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
