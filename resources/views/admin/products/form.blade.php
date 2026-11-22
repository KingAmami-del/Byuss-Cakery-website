@extends('admin.layouts.app')

@section('title', $product->exists ? 'Edit produk' : 'Tambah produk')

@section('content')
    <header class="admin-page__head">
        <div>
            <p class="admin-eyebrow">Katalog toko</p>
            <h1 class="admin-title">{{ $product->exists ? 'Edit produk' : 'Tambah produk' }}</h1>
            <p class="admin-subtitle">Lengkapi detail produk di bawah ini.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="admin-button admin-button--secondary">Kembali ke produk</a>
    </header>

    <section class="admin-panel admin-panel--form">
        <form method="POST"
              action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <div class="admin-field">
                    <label for="name">Nama produk</label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
                    @error('name') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" data-product-category required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories->where('type', 'food') as $category)
                            <option value="{{ $category->id }}" data-type="food" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                        @foreach ($categories->where('type', 'drink') as $category)
                            <option value="{{ $category->id }}" data-type="drink" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                                {{ $category->name }} (Minuman)
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="admin-field__error">{{ $message }}</p> @enderror
                    @if ($categories->isEmpty())
                        <p class="admin-field__hint">Buat kategori terlebih dahulu di <a href="{{ route('admin.categories.create') }}">halaman kategori</a>.</p>
                    @endif
                </div>

                <div class="admin-field admin-field--wide">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" maxlength="2000" required>{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-form-grid admin-form-grid--nested admin-field--wide" data-price-group="food">
                    <div class="admin-field">
                        <label for="price">Harga produk (Rp)</label>
                        <input id="price" name="price" type="number" min="0" step="1" value="{{ old('price', $product->price) }}">
                        @error('price') <p class="admin-field__error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="admin-form-grid admin-form-grid--nested admin-field--wide" data-price-group="drink">
                    <div class="admin-field">
                        <label for="ice_price">Harga Ice (Rp)</label>
                        <input id="ice_price" name="ice_price" type="number" min="0" step="1" value="{{ old('ice_price', $product->ice_price) }}">
                        @error('ice_price') <p class="admin-field__error">{{ $message }}</p> @enderror
                    </div>
                    <div class="admin-field">
                        <label for="hot_price">Harga Hot (Rp)</label>
                        <input id="hot_price" name="hot_price" type="number" min="0" step="1" value="{{ old('hot_price', $product->hot_price) }}">
                        @error('hot_price') <p class="admin-field__error">{{ $message }}</p> @enderror
                    </div>
                    <p class="admin-field__hint admin-field--wide">Isi minimal satu harga untuk pilihan Ice atau Hot.</p>
                </div>

                <div class="admin-field">
                    <label for="stock">Stok (unit)</label>
                    <input id="stock" name="stock" type="number" min="0" step="1"
                           value="{{ old('stock', $product->exists ? $product->stock : 0) }}">
                    @error('stock') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label for="image">Foto produk</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">
                    <p class="admin-field__hint">Format JPG, PNG, atau WebP. Maksimal 5 MB.</p>
                    @error('image') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>

                @if ($product->imageUrl())
                    <div class="admin-field admin-field--wide">
                        <div class="admin-current-image">
                            <img src="{{ $product->imageUrl() }}" alt="Foto {{ $product->name }}">
                            <label class="admin-check">
                                <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                                <span>Hapus foto saat disimpan</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="admin-field admin-field--wide">
                    <input type="hidden" name="is_available" value="0">
                    <label class="admin-check" for="is_available">
                        <input id="is_available" type="checkbox" name="is_available" value="1"
                               @checked(old('is_available', $product->exists ? $product->is_available : true))>
                        <span>Produk tersedia untuk dipesan</span>
                    </label>
                    @error('is_available') <p class="admin-field__error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-button">{{ $product->exists ? 'Simpan perubahan' : 'Tambah produk' }}</button>
                <a href="{{ route('admin.products.index') }}" class="admin-button admin-button--secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var category = document.querySelector('[data-product-category]');
            if (!category) return;

            function updatePriceFields() {
                var selected = category.options[category.selectedIndex];
                var type = selected ? selected.dataset.type : null;

                document.querySelectorAll('[data-price-group]').forEach(function (group) {
                    group.hidden = !type || group.dataset.priceGroup !== type;
                });
            }

            category.addEventListener('change', updatePriceFields);
            updatePriceFields();
        })();
    </script>
@endpush
