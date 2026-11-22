<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(): View
    {
        return view('checkout');
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $token = Str::random(64);

        $order = DB::transaction(function () use ($data, $token): Order {
            $products = Product::query()
                ->with('category')
                ->whereIn('slug', collect($data['items'])->pluck('slug')->unique())
                ->lockForUpdate()
                ->get()
                ->keyBy('slug');

            $lines = $this->pricedLines($data['items'], $products);
            $totalAmount = array_sum(array_map(
                static fn (array $line): int => $line['unit_price'] * $line['quantity'],
                $lines,
            ));

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'access_token_hash' => hash('sha256', $token),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'customer_note' => $data['customer_note'] ?? null,
                'payment_method' => 'qris',
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_AWAITING_PAYMENT,
            ]);

            foreach ($lines as $line) {
                $line['line_total'] = $line['unit_price'] * $line['quantity'];
                $order->items()->create($line);
            }

            return $order;
        });

        return redirect()->route('orders.payment', [
            'order' => $order->order_number,
            'token' => $token,
        ]);
    }

    /**
     * @param  array<int, array{slug: string, variant?: string|null, quantity: int}>  $requestedItems
     * @param  Collection<string, Product>  $products
     * @return array<string, array{product_id: int, product_name: string, product_description: ?string, product_category: string, variant: string|null, unit_price: int, quantity: int}>
     */
    private function pricedLines(array $requestedItems, Collection $products): array
    {
        $lines = [];

        foreach ($requestedItems as $requestedItem) {
            $product = $products->get($requestedItem['slug']);
            if (! $product || ! $product->is_available) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu produk sudah tidak tersedia.',
                ]);
            }

            $variant = $requestedItem['variant'] ?? null;
            if ($product->category->type === 'drink') {
                $price = match ($variant) {
                    'ice' => $product->ice_price,
                    'hot' => $product->hot_price,
                    default => null,
                };
            } else {
                $price = $variant === null ? $product->price : null;
            }

            if ($price === null) {
                throw ValidationException::withMessages([
                    'items' => 'Pilihan produk atau suhu minuman tidak valid.',
                ]);
            }

            $key = $product->slug.':'.($variant ?? 'default');
            if (! isset($lines[$key])) {
                $lines[$key] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_category' => $product->category->name,
                    'variant' => $variant,
                    'unit_price' => $price,
                    'quantity' => 0,
                ];
            }

            $lines[$key]['quantity'] += $requestedItem['quantity'];
        }

        return $lines;
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'BYU-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
