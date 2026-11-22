@extends('layouts.app')

@section('title', 'Katalog')

@section('content')
<section class="section cat" data-catalog>
    <div class="container">

        <header class="cat__head">
            <p class="cat__eyebrow">Menu Byuss Cakery</p>
            <h1 class="cat__title">Katalog Manis Kami</h1>
            <p class="cat__lead">{{ $foodCount }} dessert dan {{ $drinkCount }} minuman, dibuat segar setiap hari. Pilih menu dan lihat harganya.</p>
        </header>

        {{-- Filter --}}
        <div class="cat__tools">
            <div class="cat__tabs" role="tablist" aria-label="Jenis menu">
                <button type="button" class="cat-tab is-on" role="tab" aria-selected="true" data-cat-type="all">Semua <span>{{ $total }}</span></button>
                <button type="button" class="cat-tab" role="tab" aria-selected="false" data-cat-type="food">Dessert <span>{{ $foodCount }}</span></button>
                <button type="button" class="cat-tab" role="tab" aria-selected="false" data-cat-type="drink">Minuman <span>{{ $drinkCount }}</span></button>
            </div>

            <label class="cat__search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>
                <input type="search" placeholder="Cari menu, mis. matcha" data-cat-search aria-label="Cari menu">
            </label>
        </div>

        <div class="cat__chips" aria-label="Kategori">
            <button type="button" class="cat-chip is-on" data-cat-group="all" data-for="all">Semua kategori</button>
            @foreach ($groups as $g)
                <button type="button" class="cat-chip" data-cat-group="{{ $g['name'] }}" data-for="{{ $g['type'] }}">{{ $g['name'] }}</button>
            @endforeach
        </div>

        <p class="cat__count" data-cat-count aria-live="polite">Menampilkan {{ $total }} menu</p>

        {{-- Daftar per kategori --}}
        @foreach ($groups as $g)
            <section class="cat-group" data-group-section="{{ $g['name'] }}" data-type="{{ $g['type'] }}">
                <h2 class="cat-group__title">{{ $g['name'] }} <small>{{ count($g['items']) }} menu</small></h2>

                <div class="cat-grid" tabindex="0" role="region" aria-label="Daftar menu {{ $g['name'] }}">
                    @foreach ($g['items'] as $it)
                        @php
                            $variants = array_values(array_filter(['ice', 'hot'], fn ($v) => ! empty($it[$v] ?? null)));
                            $first = $variants[0] ?? null;
                            $data = [
                                'slug'  => $it['slug'],
                                'name'  => $it['name'],
                                'group' => $it['group'],
                                'image' => $it['image'],
                                'desc'  => $it['desc'],
                                'price' => $it['price'],
                                'ice'   => $it['ice'] ?? null,
                                'hot'   => $it['hot'] ?? null,
                            ];
                        @endphp

                        <article class="pcard" id="p-{{ $it['slug'] }}"
                                 data-type="{{ $it['type'] }}"
                                 data-group="{{ $it['group'] }}"
                                 data-slug="{{ $it['slug'] }}"
                                 data-available="{{ $it['is_available'] ? 'true' : 'false' }}"
                                 data-search="{{ \Illuminate\Support\Str::lower($it['name'] . ' ' . $it['group'] . ' ' . $it['desc']) }}"
                                 data-item="{{ json_encode($data) }}">
                            @if ($it['image'])
                                <div class="pcard__photo">
                                    <img src="{{ $it['image'] }}" alt="{{ $it['name'] }}" loading="lazy">
                                </div>
                            @else
                                <div class="pcard__photo is-empty">
                                    <span>Gambar segera hadir</span>
                                </div>
                            @endif

                            <div class="pcard__body">
                                <h3 class="pcard__name">{{ $it['name'] }}</h3>
                                <p class="pcard__desc">{{ $it['desc'] }}</p>
                                @unless ($it['is_available'])
                                    <p class="pcard__status" role="status">Habis</p>
                                @endunless

                                @if ($it['type'] === 'drink')
                                    @if (count($variants) > 1)
                                        <div class="kq-seg" role="radiogroup" aria-label="Pilih suhu minuman">
                                            @foreach ($variants as $v)
                                                <button type="button" class="kq-seg__btn @if ($v === $first) is-on @endif" role="radio"
                                                        aria-checked="{{ $v === $first ? 'true' : 'false' }}" data-variant="{{ $v }}">{{ $v === 'ice' ? 'Ice' : 'Hot' }}</button>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="pcard__only">Hanya tersedia {{ $first === 'ice' ? 'Ice' : 'Hot' }}</p>
                                    @endif
                                @endif

                                <div class="pcard__foot">
                                    @if ($first)
                                        <strong class="pcard__price" data-price>Rp {{ number_format($it[$first], 0, ',', '.') }}</strong>
                                    @elseif ($it['price'] !== null)
                                        <strong class="pcard__price" data-price>Rp {{ number_format($it['price'], 0, ',', '.') }}</strong>
                                    @else
                                        <span class="pcard__soon">Harga menyusul</span>
                                    @endif
                                    <button type="button" class="btn btn--primary btn--small pcard__add" data-add data-variant-now="{{ $first }}" aria-label="{{ $it['is_available'] ? 'Tambah ' . $it['name'] . ' ke keranjang' : $it['name'] . ' habis' }}" @disabled(! $it['is_available'])>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h13l-1.5 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.6L5.5 4H3"/><circle cx="10" cy="20.5" r="1"/><circle cx="16" cy="20.5" r="1"/></svg>
                                        <span>{{ $it['is_available'] ? 'Tambah ke keranjang' : 'Habis' }}</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="cat__empty" data-cat-empty hidden>
            <p>Menu yang kamu cari belum ketemu.</p>
            <button type="button" class="btn btn--ghost" data-cat-reset>Tampilkan semua menu</button>
        </div>
    </div>
</section>

@include('partials.cart')
@endsection

@push('head')
    {{-- quiz.css memuat gaya keranjang & tombol Ice/Hot yang dipakai di sini --}}
    <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/catalog.js') }}" defer></script>
@endpush
