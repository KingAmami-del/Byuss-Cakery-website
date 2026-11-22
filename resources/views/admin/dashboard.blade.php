@extends('admin.layouts.app')

@section('title', 'Ringkasan')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Panel pengelola toko</p>
            <h1 class="admin-title">Halo, {{ auth()->user()->name }} ✿</h1>
            <p class="admin-subtitle">Atur produk dan kategori katalog Byuss Cakery dari satu tempat.</p>
        </div>
    </header>

    <section class="admin-stats" aria-label="Ringkasan katalog">
        <article class="admin-stat">
            <span class="admin-stat__label">Total produk</span>
            <strong>{{ $productCount }}</strong>
            <span>menu dalam katalog</span>
        </article>
        <article class="admin-stat">
            <span class="admin-stat__label">Produk tersedia</span>
            <strong>{{ $availableProductCount }}</strong>
            <span>ditampilkan siap dipesan</span>
        </article>
        <article class="admin-stat">
            <span class="admin-stat__label">Kategori</span>
            <strong>{{ $categoryCount }}</strong>
            <span>kelompok menu</span>
        </article>
    </section>

    <section class="admin-manage" aria-label="Pengelolaan toko">
        <a href="{{ route('admin.orders.index') }}" class="admin-manage__card">
            <span class="admin-manage__icon" aria-hidden="true">▤</span>
            <span>
                <strong>Kelola pesanan</strong>
                <small>Lihat bukti pembayaran dan konfirmasi pesanan pelanggan.</small>
            </span>
            <span class="admin-manage__arrow" aria-hidden="true">&rarr;</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="admin-manage__card">
            <span class="admin-manage__icon" aria-hidden="true">✿</span>
            <span>
                <strong>Kelola produk</strong>
                <small>Tambah menu, ubah foto, harga, stok, dan status produk.</small>
            </span>
            <span class="admin-manage__arrow" aria-hidden="true">&rarr;</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="admin-manage__card">
            <span class="admin-manage__icon" aria-hidden="true">▦</span>
            <span>
                <strong>Kelola kategori</strong>
                <small>Tambah, ubah nama, dan hapus kategori katalog.</small>
            </span>
            <span class="admin-manage__arrow" aria-hidden="true">&rarr;</span>
        </a>
    </section>
@endsection
