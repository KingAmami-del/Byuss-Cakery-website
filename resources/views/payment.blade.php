@extends('layouts.app')

@section('title', 'Pembayaran '.$order->order_number)

@section('content')
    @php
        $statusLabels = [
            \App\Models\Order::STATUS_AWAITING_PAYMENT => 'Menunggu pembayaran',
            \App\Models\Order::STATUS_AWAITING_CONFIRMATION => 'Menunggu konfirmasi admin',
            \App\Models\Order::STATUS_PAID => 'Pembayaran dikonfirmasi',
            \App\Models\Order::STATUS_REJECTED => 'Bukti pembayaran perlu diperbaiki',
            \App\Models\Order::STATUS_SHIPPED => 'Pesanan dikirim',
            \App\Models\Order::STATUS_DELIVERED => 'Pesanan tiba',
        ];
    @endphp
    <section class="checkout-page">
        <div class="container checkout-page__inner checkout-page__inner--narrow">
            <header class="checkout-heading">
                <p class="eyebrow">Konfirmasi pesanan</p>
                <h1 class="h1">Pembayaran QRIS</h1>
                <p class="lead">Status: <strong>{{ $statusLabels[$order->status] ?? $order->status }}</strong></p>
            </header>

            @include('partials.order-code', ['orderNumber' => $order->order_number])
            @include('partials.order-progress', ['order' => $order])

            @if (session('status'))
                <div class="checkout-alert checkout-alert--success" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="checkout-alert checkout-alert--error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="checkout-panel payment-panel">
                @if ($order->status === \App\Models\Order::STATUS_AWAITING_PAYMENT)
                    <p class="checkout-alert checkout-alert--success" role="status">
                        Terima kasih sudah memesan! Admin akan mengonfirmasi pesanan Anda melalui WhatsApp.
                    </p>
                @endif
                <div class="qris-payment">
                    <img src="{{ asset('images/qris-byuss.png') }}" alt="Kode QRIS Byuss Cakery untuk pembayaran pelanggan">
                </div>
                <p class="checkout-hint">Pindai kode QRIS di atas menggunakan aplikasi bank atau dompet digital Anda.</p>

                <ul class="payment-items">
                    @foreach ($order->items as $item)
                        <li>
                            <span>{{ $item->product_name }}@if ($item->variant) · {{ $item->variant === 'ice' ? 'Ice' : 'Hot' }}@endif × {{ $item->quantity }}</span>
                            <strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong>
                        </li>
                    @endforeach
                </ul>
                <p class="checkout-total">Total pembayaran <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>

                @if (in_array($order->status, [\App\Models\Order::STATUS_AWAITING_PAYMENT, \App\Models\Order::STATUS_REJECTED], true))
                    @if ($order->admin_note)
                        <p class="checkout-alert checkout-alert--error">{{ $order->admin_note }}</p>
                    @endif
                    <form class="checkout-form" method="POST" action="{{ route('orders.payment-proof', ['order' => $order->order_number, 'token' => $token]) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="checkout-field">
                            <span>Foto bukti pembayaran</span>
                            <input type="file" name="proof" accept="image/jpeg,image/png,image/webp" required>
                            <small>JPG, PNG, atau WEBP; ukuran maksimal 5 MB.</small>
                        </label>
                        <button class="btn btn--primary checkout-submit" type="submit">Kirim bukti pembayaran</button>
                    </form>
                @elseif ($order->status === \App\Models\Order::STATUS_AWAITING_CONFIRMATION)
                    <p class="checkout-alert checkout-alert--success">Bukti pembayaran sudah diterima dan sedang diperiksa oleh admin.</p>
                    <a class="btn btn--ghost order-track-button" href="{{ route('orders.track.show', $order->order_number) }}">Lacak pesanan</a>
                @elseif ($order->status === \App\Models\Order::STATUS_PAID)
                    <p class="checkout-alert checkout-alert--success">Terima kasih, pesanan telah dikonfirmasi.</p>
                    <a class="btn btn--primary order-track-button" href="{{ route('orders.track.show', $order->order_number) }}">Lacak pesanan</a>
                @elseif ($order->status === \App\Models\Order::STATUS_SHIPPED)
                    <p class="checkout-alert checkout-alert--success">Pesanan telah dikirim.</p>
                    <a class="btn btn--ghost order-track-button" href="{{ route('orders.track.show', $order->order_number) }}">Lacak pesanan</a>
                @else
                    <p class="checkout-alert checkout-alert--success">Pesanan telah tiba. Terima kasih telah memesan.</p>
                    <a class="btn btn--primary order-track-button" href="{{ route('orders.track.show', $order->order_number) }}">Lacak pesanan</a>
                @endif
            </section>
        </div>
    </section>
    @include('partials.cart')
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-status.css') }}">
@endpush

@push('scripts')
    <script>localStorage.removeItem('byuss_cart_v1');</script>
    <script src="{{ asset('js/order-code.js') }}" defer></script>
@endpush
