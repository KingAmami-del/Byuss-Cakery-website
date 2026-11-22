<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentProofRequest;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PaymentController extends Controller
{
    public function show(Order $order, string $token): View
    {
        $this->authorizeOrderAccess($order, $token);

        return view('payment', [
            'order' => $order->load('items'),
            'token' => $token,
        ]);
    }

    public function storeProof(
        StorePaymentProofRequest $request,
        Order $order,
        string $token,
    ): RedirectResponse {
        $this->authorizeOrderAccess($order, $token);

        $oldPath = DB::transaction(function () use ($request, $order): ?string {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! in_array($lockedOrder->status, [
                Order::STATUS_AWAITING_PAYMENT,
                Order::STATUS_REJECTED,
            ], true)) {
                throw ValidationException::withMessages([
                    'proof' => 'Bukti pembayaran sedang diperiksa atau pembayaran sudah dikonfirmasi.',
                ]);
            }

            $path = $request->file('proof')->store('payment-proofs', 'local');
            if (! is_string($path)) {
                throw new RuntimeException('Bukti pembayaran tidak berhasil disimpan.');
            }

            $previousPath = $lockedOrder->proof_path;
            $lockedOrder->update([
                'proof_path' => $path,
                'status' => Order::STATUS_AWAITING_CONFIRMATION,
                'payment_submitted_at' => now(),
                'admin_note' => null,
            ]);

            return $previousPath;
        });

        if ($oldPath && ! Storage::disk('local')->delete($oldPath)) {
            Log::warning('Bukti pembayaran lama tidak berhasil dihapus.', [
                'order_id' => $order->id,
                'proof_path' => $oldPath,
            ]);
        }

        return redirect()->route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ])->with('status', 'Bukti pembayaran berhasil dikirim untuk diperiksa.');
    }

    private function authorizeOrderAccess(Order $order, string $token): void
    {
        abort_unless(hash_equals($order->access_token_hash, hash('sha256', $token)), 404);
    }
}
