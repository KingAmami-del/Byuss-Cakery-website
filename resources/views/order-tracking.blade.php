@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
    @php
        $statusLabels = [
            \App\Models\Order::STATUS_AWAITING_PAYMENT => 'Menunggu pembayaran',
            \App\Models\Order::STATUS_AWAITING_CONFIRMATION => 'Bukti pembayaran sedang diperiksa',
            \App\Models\Order::STATUS_PAID => 'Pembayaran terkonfirmasi',
            \App\Models\Order::STATUS_REJECTED => 'Bukti pembayaran ditolak. Silakan gunakan tautan pembayaran sebelumnya untuk mengirim ulang bukti.',
            \App\Models\Order::STATUS_SHIPPED => 'Pesanan dikirim',
            \App\Models\Order::STATUS_DELIVERED => 'Pesanan tiba',
        ];
    @endphp

    <section class="checkout-page">
        <div class="container checkout-page__inner checkout-page__inner--narrow">
            <header class="checkout-heading">
                <p class="eyebrow">Pembaruan pesanan</p>
                <h1 class="h1">Lacak Pesanan</h1>
                <p class="lead">Masukkan kode pesanan untuk melihat status terbaru. Login tidak diperlukan.</p>
            </header>

            @if ($errors->any())
                <div class="checkout-alert checkout-alert--error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="checkout-panel checkout-form" method="POST" action="{{ route('orders.track.store') }}">
                @csrf
                <label class="checkout-field">
                    <span>Kode pesanan</span>
                    <input
                        type="text"
                        name="order_number"
                        value="{{ old('order_number', $searchedCode) }}"
                        placeholder="Contoh: BYU-20261120-ABC123"
                        autocomplete="off"
                        autocapitalize="characters"
                        required
                        maxlength="32"
                    >
                </label>
                <button class="btn btn--primary checkout-submit" type="submit">Lacak pesanan</button>
            </form>

            @if ($hasSearched)
                @if ($order)
                    <section class="checkout-panel order-track-result" aria-live="polite">
                        @include('partials.order-code', ['orderNumber' => $order->order_number])
                        <p class="eyebrow order-track-result__eyebrow">Status pesanan</p>
                        <h2>{{ $statusLabels[$order->status] ?? 'Status sedang diperbarui' }}</h2>
                        @include('partials.order-progress', ['order' => $order])
                    </section>
                @else
                    <p class="checkout-alert checkout-alert--error order-track-result" role="status">
                        Kode pesanan tidak ditemukan. Periksa kembali kode yang tertera pada halaman pembayaran.
                    </p>
                @endif
            @endif
        </div>
    </section>
    @include('partials.cart')
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-tracking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-status.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/order-code.js') }}" defer></script>
@endpush
