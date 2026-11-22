@extends('admin.layouts.app')

@section('title', 'Kategori')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Pengelompokan katalog</p>
            <h1 class="admin-title">Kategori</h1>
            <p class="admin-subtitle">Atur kategori dessert dan minuman yang dipakai produk.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="admin-button">+ Tambah kategori</a>
    </header>

    <section class="admin-panel">
        @if ($categories->isEmpty())
            <div class="admin-empty">
                <strong>Belum ada kategori</strong>
                <p>Buat kategori agar produk dapat dikelompokkan di katalog.</p>
                <a href="{{ route('admin.categories.create') }}" class="admin-button">Tambah kategori</a>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama kategori</th>
                            <th>Jenis</th>
                            <th>Produk</th>
                            <th><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->type === 'food' ? 'Dessert' : 'Minuman' }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <div class="admin-row-actions">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="admin-link-button">Edit nama</a>
                                        @if ($category->products_count === 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-link-button admin-link-button--danger">Hapus</button>
                                            </form>
                                        @else
                                            <span class="admin-cell-sub">Pindahkan produk sebelum menghapus</span>
                                        @endif
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
