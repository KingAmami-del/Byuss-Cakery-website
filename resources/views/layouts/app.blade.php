<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') — {{ config('byuss.name') }}</title>
    <meta name="description" content="Byuss Cakery: patisserie kecil, kebahagiaan besar. Dessert yang diracik dengan hati.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body>

<div class="announce">{{ config('byuss.announcement') }}</div>

<header class="nav">
    <div class="container nav__inner">
        <a href="{{ route('home') }}" class="logo" aria-label="Byuss Cakery, ke beranda">
            <span class="logo__word">byuss</span>
            <span class="logo__sub">CAKERY</span>
        </a>

        <button class="nav__toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="menu">
            <span></span><span></span><span></span>
        </button>

        <nav id="menu" class="nav__menu" aria-label="Menu utama">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('catalog') }}" @class(['is-active' => request()->routeIs('catalog')])>Katalog</a>
            <a href="{{ route('birthday-cake') }}" @class(['is-active' => request()->routeIs('birthday-cake')])>Birthday Cake</a>
            <a href="{{ route('orders.track.create') }}" @class(['is-active' => request()->routeIs('orders.track.*')])>Lacak Pesanan</a>
        </nav>

        <a href="{{ route('cart') }}" class="cart" data-cart-open aria-label="Buka keranjang">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h12l-1 12H7L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>
            <span>Keranjang (<span data-cart-count>0</span>)</span>
        </a>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="container footer__inner">
        <div>
            <span class="logo__word logo__word--light">byuss</span>
            <p>Patisserie kecil, kebahagiaan besar.</p>
        </div>
        <div class="footer__links">
            <a href="{{ route('catalog') }}">Katalog</a>
            <a href="{{ route('birthday-cake') }}">Birthday Cake</a>
            <a href="{{ route('orders.track.create') }}">Lacak Pesanan</a>
        </div>
        <p class="footer__copy">&copy; {{ date('Y') }} {{ config('byuss.name') }}. Semua hak dilindungi.</p>
    </div>
</footer>

<script>window.BYUSS_CHECKOUT_URL = @json(route('checkout.create'));</script>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
