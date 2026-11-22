<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') · Dashboard Admin Byuss Cakery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="adm">
    <header class="bar">
        <a href="{{ route('admin.dashboard') }}" class="bar__brand">Byuss Cakery <span>Admin</span></a>
        <nav class="bar__nav" aria-label="Navigasi admin">
            <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Ringkasan</a>
            <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>Pesanan</a>
            <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>Produk</a>
            <a href="{{ route('admin.categories.index') }}" @class(['is-active' => request()->routeIs('admin.categories.*')])>Kategori</a>
        </nav>
        <div class="bar__right">
            <a href="{{ url('/') }}" class="bar__link" target="_blank" rel="noopener">Lihat situs</a>
            <span class="bar__user">{{ auth()->user()->email }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="ghost">Keluar</button>
            </form>
        </div>
    </header>

    <main class="admin-page">
        @if (session('status'))
            <div class="admin-notice admin-notice--success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="admin-notice admin-notice--error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
