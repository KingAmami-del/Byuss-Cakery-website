<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_track_order_status_without_exposing_customer_details(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'BYU-20261120-ABC123',
            'status' => Order::STATUS_AWAITING_CONFIRMATION,
            'customer_name' => 'Private Customer',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Rahasia No. 123',
        ]);

        $this->post(route('orders.track.store'), [
            'order_number' => strtolower($order->order_number),
        ])->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Bukti pembayaran sedang diperiksa')
            ->assertDontSee('Private Customer')
            ->assertDontSee('081234567890')
            ->assertDontSee('Jl. Rahasia No. 123');
    }

    public function test_tracking_page_does_not_confirm_whether_an_unknown_order_code_exists(): void
    {
        $this->post(route('orders.track.store'), [
            'order_number' => 'BYU-20261120-ABC123',
        ])->assertOk()
            ->assertSee('Kode pesanan tidak ditemukan. Periksa kembali kode yang tertera pada halaman pembayaran.');
    }

    public function test_customer_can_open_tracking_result_directly_with_the_order_code(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'BYU-20261120-XYZ789',
            'status' => Order::STATUS_DELIVERED,
        ]);

        $this->get(route('orders.track.show', $order->order_number))
            ->assertOk()
            ->assertSee('Salin kode')
            ->assertSee('Terkonfirmasi / ditolak')
            ->assertSee('Pesanan dikirim')
            ->assertSee('Pesanan tiba')
            ->assertSee('Pesanan telah tiba');
    }

    public function test_tracking_rejects_an_invalid_order_code_format(): void
    {
        $this->from(route('orders.track.create'))
            ->post(route('orders.track.store'), [
                'order_number' => 'not-a-code',
            ])->assertRedirect(route('orders.track.create'))
            ->assertSessionHasErrors([
                'order_number' => 'Format kode pesanan tidak valid.',
            ]);
    }

    public function test_tracking_search_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('orders.track.store'), [
                'order_number' => 'BYU-20261120-ABC123',
            ])->assertOk();
        }

        $this->post(route('orders.track.store'), [
            'order_number' => 'BYU-20261120-ABC123',
        ])->assertTooManyRequests();
    }
}
