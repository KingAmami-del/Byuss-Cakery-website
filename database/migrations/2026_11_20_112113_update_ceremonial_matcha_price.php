<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('products')
            ->where('slug', 'matcha-ceremonial-matcha')
            ->where('ice_price', 38000)
            ->update(['ice_price' => 42000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')
            ->where('slug', 'matcha-ceremonial-matcha')
            ->where('ice_price', 42000)
            ->update(['ice_price' => 38000]);
    }
};
