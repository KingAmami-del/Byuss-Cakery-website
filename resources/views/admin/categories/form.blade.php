@extends('admin.layouts.app')

@section('title', $category->exists ? 'Edit kategori' : 'Tambah kategori')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Pengelompokan katalog</p>
            <h1 class="admin-title">{{ $category->exists ? 'Edit kategori' : 'Tambah kategori' }}</h1>
            <p class="admin-subtitle">Nama kategori akan tampil di katalog dan pilihan produk.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="admin-button admin-button--secondary">Kembali ke kategori</a>
    </header>

    <section class="admin-panel admin-panel--form admin-panel--narrow">
        <form method="POST"
              action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div class="admin-field">
                <label for="name">Nama kategori</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="100" required autofocus>
                @error('name') <p class="admin-field__error">{{ $message }}</p> @enderror
            </div>

            @if ($category->exists)
                <div class="admin-field">
                    <label>Jenis kategori</label>
                    <div class="admin-readonly">{{ $category->type === 'food' ? 'Dessert' : 'Minuman' }}</div>
                    <p class="admin-field__hint">Jenis tidak dapat diubah karena menentukan kelompok menu.</p>
                </div>
            @else
                <div class="admin-field">
                    <label for="type">Jenis kategori</label>
                    <select id="type" name="type" required>
                        <option value="">Pilih jenis</option>
                        <option value="food" @selected(old('type') === 'food')>Dessert</option>
                        <option value="drink" @selected(old('type') === 'drink')>Minuman</option>
                    </select>
                    @error('type') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="admin-form-actions">
                <button type="submit" class="admin-button">{{ $category->exists ? 'Simpan nama' : 'Tambah kategori' }}</button>
                <a href="{{ route('admin.categories.index') }}" class="admin-button admin-button--secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
