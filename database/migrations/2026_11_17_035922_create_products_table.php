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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('ice_price')->nullable();
            $table->unsignedBigInteger('hot_price')->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->boolean('is_available')->default(true);
            $table->index(['category_id', 'is_available']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
