<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackOrderRequest;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderTrackingController extends Controller
{
    public function create(): View
    {
        return view('order-tracking', [
            'order' => null,
            'searchedCode' => null,
            'hasSearched' => false,
        ]);
    }

    public function store(TrackOrderRequest $request): View
    {
        return $this->trackingResult($request->validated('order_number'));
    }

    public function show(string $orderNumber): View
    {
        return $this->trackingResult($orderNumber);
    }

    private function trackingResult(string $orderNumber): View
    {
        $code = strtoupper(trim($orderNumber));
        $order = Order::query()
            ->select(['order_number', 'status'])
            ->where('order_number', $code)
            ->first();

        return view('order-tracking', [
            'order' => $order,
            'searchedCode' => $code,
            'hasSearched' => true,
        ]);
    }
}
