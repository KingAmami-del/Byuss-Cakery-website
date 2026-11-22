@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <section class="checkout-page">
        <div class="container checkout-page__inner">
            <header class="checkout-heading">
                <p class="eyebrow">Langkah terakhir</p>
                <h1 class="h1">Checkout pesanan</h1>
                <p class="lead">Periksa pesananmu dan isi kontak agar kami dapat menghubungimu.</p>
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

            <div class="checkout-grid">
                <section class="checkout-panel">
                    <h2>Ringkasan produk</h2>
                    <ul class="checkout-items" data-checkout-items>
                        <li class="checkout-empty">Memuat keranjang...</li>
                    </ul>
                    <p class="checkout-total">Total sementara <strong data-checkout-total>Rp 0</strong></p>
                    <p class="checkout-hint">Total final dihitung kembali berdasarkan harga produk saat ini.</p>
                    <a class="checkout-back" href="{{ route('catalog') }}">← Kembali ke katalog</a>
                </section>

                <form class="checkout-panel checkout-form" method="POST" action="{{ route('checkout.store') }}" data-checkout-form>
                    @csrf
                    <h2>Data pemesan</h2>
                    <label class="checkout-field">
                        <span>Nama lengkap</span>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" required maxlength="120">
                    </label>
                    <label class="checkout-field">
                        <span>Nomor WhatsApp</span>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" autocomplete="tel" required maxlength="30" placeholder="Contoh: 081234567890">
                    </label>
                    <label class="checkout-field">
                        <span>Alamat pengiriman</span>
                        <textarea name="customer_address" rows="3" autocomplete="street-address" required minlength="5" maxlength="1000" placeholder="Tuliskan alamat lengkap untuk pengiriman">{{ old('customer_address') }}</textarea>
                    </label>
                    <label class="checkout-field">
                        <span>Catatan <small>(opsional)</small></span>
                        <textarea name="customer_note" rows="3" maxlength="1000">{{ old('customer_note') }}</textarea>
                    </label>
                    <p class="checkout-hint" data-checkout-empty hidden>Keranjang kosong. Silakan pilih produk terlebih dahulu.</p>
                    <button class="btn btn--primary checkout-submit" type="submit" data-checkout-submit>Pesan dan lanjut bayar</button>
                </form>
            </div>
        </div>
    </section>
    @include('partials.cart')
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/checkout.js') }}" defer></script>
@endpush
