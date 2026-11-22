@extends('admin.layouts.app')

@section('title', 'Produk')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Katalog toko</p>
            <h1 class="admin-title">Produk</h1>
            <p class="admin-subtitle">Kelola nama, kategori, foto, harga, stok, dan ketersediaan produk.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="admin-button">+ Tambah produk</a>
    </header>

    <section class="admin-panel">
        @if ($products->isEmpty())
            <div class="admin-empty">
                <strong>Belum ada produk</strong>
                <p>Tambahkan produk pertama untuk mengisi katalog toko.</p>
                <a href="{{ route('admin.products.create') }}" class="admin-button">Tambah produk</a>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <div class="admin-product">
                                        <div class="admin-product__image">
                                            @if ($product->imageUrl())
                                                <img src="{{ $product->imageUrl() }}" alt="" loading="lazy">
                                            @else
                                                <span aria-hidden="true">{{ mb_substr($product->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <small>{{ $product->description }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>
                                    @if ($product->category->type === 'drink')
                                        <span>Ice: {{ $product->ice_price !== null ? 'Rp '.number_format($product->ice_price, 0, ',', '.') : '—' }}</span>
                                        <small class="admin-cell-sub">Hot: {{ $product->hot_price !== null ? 'Rp '.number_format($product->hot_price, 0, ',', '.') : '—' }}</small>
                                    @else
                                        {{ $product->price !== null ? 'Rp '.number_format($product->price, 0, ',', '.') : 'Belum diisi' }}
                                    @endif
                                </td>
                                <td>{{ $product->stock ?? 'Belum diatur' }}</td>
                                <td>
                                    <span class="admin-badge {{ $product->is_available ? 'admin-badge--available' : 'admin-badge--unavailable' }}">
                                        {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-row-actions">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="admin-link-button">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-link-button admin-link-button--danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
