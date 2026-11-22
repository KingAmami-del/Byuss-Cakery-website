<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_order_management(): void
    {
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_manage_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_confirm_a_payment(): void
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_CONFIRMATION,
            'proof_path' => 'payment-proofs/proof.jpg',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.review', $order), [
            'status' => Order::STATUS_PAID,
        ]);

        $response->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('status', 'Pembayaran berhasil dikonfirmasi.');
        $this->assertSame(Order::STATUS_PAID, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_admin_can_reject_a_payment_without_a_note(): void
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_CONFIRMATION,
            'proof_path' => 'payment-proofs/proof.jpg',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.review', $order), [
            'status' => Order::STATUS_REJECTED,
        ]);

        $response->assertRedirect(route('admin.orders.index'))
            ->assertSessionHas('status', 'Bukti pembayaran ditolak.');
        $this->assertSame(Order::STATUS_REJECTED, $order->fresh()->status);
        $this->assertNull($order->fresh()->admin_note);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertDontSee($order->order_number)
            ->assertSee('Bukti pembayaran ditolak.');
        $this->assertModelExists($order->fresh());
    }

    public function test_admin_can_mark_a_confirmed_order_as_shipped(): void
    {
        $admin = $this->createAdmin();
        $paidAt = now()->subDay();
        $order = Order::factory()->create([
            'status' => Order::STATUS_PAID,
            'paid_at' => $paidAt,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Konfirmasi pesanan dikirim');

        $response = $this->actingAs($admin)->patch(route('admin.orders.review', $order), [
            'status' => Order::STATUS_SHIPPED,
        ]);

        $response->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('status', 'Pesanan ditandai telah dikirim.');
        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);
        $this->assertSame($paidAt->format('Y-m-d H:i:s'), $order->fresh()->paid_at->format('Y-m-d H:i:s'));
    }

    public function test_admin_can_mark_a_shipped_order_as_delivered(): void
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create([
            'status' => Order::STATUS_SHIPPED,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Konfirmasi pesanan tiba');

        $response = $this->actingAs($admin)->patch(route('admin.orders.review', $order), [
            'status' => Order::STATUS_DELIVERED,
        ]);

        $response->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('status', 'Pesanan dikonfirmasi telah tiba.');
        $this->assertSame(Order::STATUS_DELIVERED, $order->fresh()->status);
    }

    public function test_admin_cannot_skip_delivery_steps(): void
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create([
            'status' => Order::STATUS_AWAITING_CONFIRMATION,
        ]);

        $this->actingAs($admin)->patch(route('admin.orders.review', $order), [
            'status' => Order::STATUS_SHIPPED,
        ])->assertSessionHasErrors([
            'status' => 'Perubahan status pesanan tidak valid.',
        ]);

        $this->assertSame(Order::STATUS_AWAITING_CONFIRMATION, $order->fresh()->status);
    }

    public function test_admin_can_view_a_private_payment_proof(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('payment-proofs/proof.jpg', 'proof contents');
        $admin = $this->createAdmin();
        $order = Order::factory()->create(['proof_path' => 'payment-proofs/proof.jpg']);

        $this->actingAs($admin)
            ->get(route('admin.orders.proof', $order))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private');
    }

    public function test_admin_order_page_escapes_customer_and_note_content(): void
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create([
            'customer_name' => '<script>alert(1)</script>',
            'customer_note' => '<img src=x onerror=alert(1)>',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertOk()
            ->assertSee(e('<script>alert(1)</script>'), false)
            ->assertSee(e('<img src=x onerror=alert(1)>'), false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }
}
