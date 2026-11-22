<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'BYU-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'access_token_hash' => hash('sha256', Str::random(48)),
            'customer_name' => fake()->name(),
            'customer_phone' => '+628123456789',
            'customer_address' => fake()->address(),
            'customer_note' => null,
            'payment_method' => 'qris',
            'total_amount' => 25000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'proof_path' => null,
            'payment_submitted_at' => null,
            'paid_at' => null,
            'admin_note' => null,
        ];
    }
}
