<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_database_prices_and_combines_matching_items(): void
    {
        $category = Category::factory()->create(['type' => 'food', 'name' => 'Dessert']);
        $product = Product::factory()->for($category)->create([
            'name' => 'Lemon Tart',
            'slug' => 'lemon-tart',
            'price' => 12000,
        ]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Melati No. 10, Kota Bandung',
            'items' => [
                ['slug' => $product->slug, 'quantity' => 2],
                ['slug' => $product->slug, 'quantity' => 1],
            ],
        ]);

        $order = Order::query()->with('items')->firstOrFail();
        $pathSegments = explode('/', trim((string) parse_url($response->headers->get('Location'), PHP_URL_PATH), '/'));
        $token = end($pathSegments);

        $response->assertRedirect(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]));
        $this->assertSame(36000, $order->total_amount);
        $this->assertSame(1, $order->items->count());
        $this->assertSame(3, $order->items->first()->quantity);
        $this->assertSame(36000, $order->items->first()->line_total);
        $this->assertSame(hash('sha256', $token), $order->access_token_hash);
        $this->assertSame('Jl. Melati No. 10, Kota Bandung', $order->customer_address);
    }

    public function test_checkout_requires_a_shipping_address(): void
    {
        $category = Category::factory()->create(['type' => 'food']);
        $product = Product::factory()->for($category)->create([
            'price' => 12000,
        ]);

        $response = $this->from(route('checkout.create'))->post(route('checkout.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'items' => [['slug' => $product->slug, 'quantity' => 1]],
        ]);

        $response->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors([
                'customer_address' => 'Alamat pengiriman wajib diisi.',
            ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_a_drink_without_a_valid_temperature(): void
    {
        $category = Category::factory()->create(['type' => 'drink']);
        $product = Product::factory()->for($category)->create([
            'price' => null,
            'ice_price' => 18000,
            'hot_price' => 20000,
        ]);

        $response = $this->from(route('checkout.create'))->post(route('checkout.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Melati No. 10, Kota Bandung',
            'items' => [['slug' => $product->slug, 'quantity' => 1]],
        ]);

        $response->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors(['items' => 'Pilihan produk atau suhu minuman tidak valid.']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_uses_the_selected_drink_price_from_the_database(): void
    {
        $category = Category::factory()->create(['type' => 'drink']);
        $product = Product::factory()->for($category)->create([
            'price' => null,
            'ice_price' => 18000,
            'hot_price' => 20000,
        ]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Melati No. 10, Kota Bandung',
            'items' => [['slug' => $product->slug, 'variant' => 'hot', 'quantity' => 1]],
        ]);

        $response->assertRedirect();
        $this->assertSame(20000, Order::query()->firstOrFail()->total_amount);
    }

    public function test_checkout_rejects_products_that_are_unavailable(): void
    {
        $category = Category::factory()->create(['type' => 'food']);
        $product = Product::factory()->for($category)->create([
            'price' => 12000,
            'is_available' => false,
        ]);

        $response = $this->from(route('checkout.create'))->post(route('checkout.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Melati No. 10, Kota Bandung',
            'items' => [['slug' => $product->slug, 'quantity' => 1]],
        ]);

        $response->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors(['items' => 'Salah satu produk sudah tidak tersedia.']);
        $this->assertDatabaseCount('orders', 0);
    }
}
