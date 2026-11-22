<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPaymentRequest;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->where('status', '!=', Order::STATUS_REJECTED)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items'),
        ]);
    }

    public function proof(Order $order): BinaryFileResponse
    {
        abort_unless(
            $order->proof_path && Storage::disk('local')->exists($order->proof_path),
            404,
        );

        $response = response()->file(Storage::disk('local')->path($order->proof_path), [
            'Content-Disposition' => 'inline; filename="payment-proof-'.$order->order_number.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

        return $response;
    }

    public function review(ReviewPaymentRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();

        $message = DB::transaction(function () use ($data, $order): string {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $allowedStatuses = match ($lockedOrder->status) {
                Order::STATUS_AWAITING_CONFIRMATION => [
                    Order::STATUS_PAID,
                    Order::STATUS_REJECTED,
                ],
                Order::STATUS_PAID => [Order::STATUS_SHIPPED],
                Order::STATUS_SHIPPED => [Order::STATUS_DELIVERED],
                default => [],
            };

            if (! in_array($data['status'], $allowedStatuses, true)) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status pesanan tidak valid.',
                ]);
            }

            $attributes = [
                'status' => $data['status'],
                'admin_note' => null,
            ];

            if ($data['status'] === Order::STATUS_PAID) {
                $attributes['paid_at'] = now();
            } elseif ($data['status'] === Order::STATUS_REJECTED) {
                $attributes['paid_at'] = null;
            }

            $lockedOrder->update($attributes);

            return match ($data['status']) {
                Order::STATUS_PAID => 'Pembayaran berhasil dikonfirmasi.',
                Order::STATUS_REJECTED => 'Bukti pembayaran ditolak.',
                Order::STATUS_SHIPPED => 'Pesanan ditandai telah dikirim.',
                Order::STATUS_DELIVERED => 'Pesanan dikonfirmasi telah tiba.',
            };
        });

        if ($data['status'] === Order::STATUS_REJECTED) {
            return redirect()->route('admin.orders.index')->with('status', $message);
        }

        return redirect()->route('admin.orders.show', $order)->with('status', $message);
    }
}
