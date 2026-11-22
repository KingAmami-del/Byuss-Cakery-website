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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->char('access_token_hash', 64);
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->text('customer_note')->nullable();
            $table->string('payment_method', 16)->default('qris');
            $table->unsignedBigInteger('total_amount');
            $table->string('status', 32)->default('awaiting_payment');
            $table->string('proof_path')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->index(['status', 'created_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
