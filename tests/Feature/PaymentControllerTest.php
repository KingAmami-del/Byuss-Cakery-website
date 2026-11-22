<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_requires_the_matching_access_token(): void
    {
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', 'correct-token'),
        ]);

        $this->get(route('orders.payment', [
            'order' => $order->order_number,
            'token' => 'incorrect-token',
        ]))->assertNotFound();
    }

    public function test_payment_page_shows_the_qris_image(): void
    {
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
        ]);

        $this->get(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->assertOk()
            ->assertSee('images/qris-byuss.png')
            ->assertSee('Kode QRIS Byuss Cakery')
            ->assertSee($order->order_number)
            ->assertSee('Salin kode');
    }

    public function test_confirmed_order_page_has_a_direct_tracking_button(): void
    {
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
            'status' => Order::STATUS_PAID,
        ]);

        $this->get(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->assertOk()
            ->assertSee('Terima kasih, pesanan telah dikonfirmasi.')
            ->assertSee('Lacak pesanan')
            ->assertSee(route('orders.track.show', $order->order_number), false);
    }

    public function test_payment_page_thanks_the_customer_and_mentions_whatsapp_confirmation(): void
    {
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
            'status' => Order::STATUS_AWAITING_PAYMENT,
        ]);

        $this->get(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->assertOk()
            ->assertSee('Terima kasih sudah memesan! Admin akan mengonfirmasi pesanan Anda melalui WhatsApp.');
    }

    public function test_customer_can_upload_a_payment_proof_to_private_storage(): void
    {
        Storage::fake('local');
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
        ]);

        $response = $this->post(route('orders.payment-proof', [
            'order' => $order->order_number,
            'token' => $token,
        ]), [
            'proof' => UploadedFile::fake()->createWithContent(
                'proof.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'),
            ),
        ]);

        $order->refresh();
        $response->assertRedirect(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->assertSessionHas('status', 'Bukti pembayaran berhasil dikirim untuk diperiksa.');
        $this->assertSame(Order::STATUS_AWAITING_CONFIRMATION, $order->status);
        $this->assertNotNull($order->payment_submitted_at);
        Storage::disk('local')->assertExists($order->proof_path);

        $this->get(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->assertOk()
            ->assertSee('Lacak pesanan')
            ->assertSee(route('orders.track.show', $order->order_number));
    }

    public function test_customer_can_resubmit_a_rejected_payment_proof(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('payment-proofs/old-proof.png', 'old proof');
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
            'status' => Order::STATUS_REJECTED,
            'proof_path' => 'payment-proofs/old-proof.png',
            'admin_note' => 'Foto kurang jelas.',
        ]);

        $this->post(route('orders.payment-proof', [
            'order' => $order->order_number,
            'token' => $token,
        ]), [
            'proof' => UploadedFile::fake()->createWithContent(
                'replacement.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'),
            ),
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_AWAITING_CONFIRMATION, $order->status);
        $this->assertNull($order->admin_note);
        Storage::disk('local')->assertMissing('payment-proofs/old-proof.png');
        Storage::disk('local')->assertExists($order->proof_path);
    }

    public function test_customer_cannot_upload_a_non_image_payment_proof(): void
    {
        Storage::fake('local');
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
        ]);

        $response = $this->from(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->post(route('orders.payment-proof', [
            'order' => $order->order_number,
            'token' => $token,
        ]), [
            'proof' => UploadedFile::fake()->create('payment.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['proof' => 'Bukti pembayaran harus berupa gambar.']);
        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->fresh()->status);
        Storage::disk('local')->assertEmpty();
    }

    public function test_customer_cannot_upload_an_image_with_an_unsupported_extension(): void
    {
        Storage::fake('local');
        $token = 'customer-order-token';
        $order = Order::factory()->create([
            'access_token_hash' => hash('sha256', $token),
        ]);

        $response = $this->from(route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]))->post(route('orders.payment-proof', [
            'order' => $order->order_number,
            'token' => $token,
        ]), [
            'proof' => UploadedFile::fake()->createWithContent(
                'proof.html',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'),
            ),
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['proof' => 'Format nama file bukti harus JPG, PNG, atau WEBP.']);
        Storage::disk('local')->assertEmpty();
    }
}
