@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- ================= HERO ================= --}}
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow">Patisserie kecil, kebahagiaan besar</p>
            <h1 class="h1 hero__title">Ada Manis Di<br>Setiap Cerita</h1>
            <p class="lead">Dari jeda sore hingga perayaan istimewa. Dessert yang diracik dengan hati, untukmu dan orang-orang tersayang.</p>
            <div class="hero__cta">
                <button type="button" class="btn btn--primary" data-quiz-open aria-controls="kuis" aria-expanded="false">Temukan pilihanmu <span aria-hidden="true">&rarr;</span></button>
                <a href="{{ route('catalog') }}" class="btn btn--ghost">Lihat katalog <span aria-hidden="true">&rarr;</span></a>
            </div>
            <p class="hero__rating"><span aria-hidden="true">☆</span> 4,9/5 dari 200+ cerita manis pelanggan</p>
        </div>

        <figure class="hero__media">
            <img src="{{ asset('images/hero-outlet-interior.jpg') }}"
                 alt="Interior outlet House of Byuss dengan etalase dessert dan rangkaian bunga"
                 width="480" height="640" fetchpriority="high">
            <figcaption class="hero__tag">
                <span class="hero__tag-label">Kunjungi kami</span>
                <strong>{{ $outlet['name'] }}</strong>
                <a href="#outlet" aria-label="Lihat info outlet">&nearr;</a>
            </figcaption>
        </figure>
    </div>

    <ul class="usp">
        <li>Dibuat segar, setiap pesanan</li>
        <li>Bahan pilihan, rasa yang jujur</li>
        <li>Hantaran personal, penuh makna</li>
    </ul>
</section>

{{-- ================= KUIS REKOMENDASI (muncul setelah tombol hero diklik) ================= --}}
@include('partials.quiz')

{{-- ================= PRODUK UNGGULAN ================= --}}
<section id="favorit" class="section section--tint">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <p class="eyebrow">Produk unggulan</p>
                <h2 class="h2">Favorit pelanggan kami</h2>
            </div>
            <a href="{{ route('catalog') }}" class="link">Lihat katalog &rarr;</a>
        </div>

        <div class="products">
            @foreach ($featured as $p)
                @php($img = 'images/products/' . $p['slug'] . '.jpg')
                <article class="product">
                    <div class="product__img">
                        @if (file_exists(public_path($img)))
                            <img src="{{ asset($img) }}" alt="{{ $p['name'] }}" loading="lazy">
                        @else
                            <span class="product__ph" aria-hidden="true">{{ mb_substr($p['name'], 0, 1) }}</span>
                        @endif
                        @if ($p['badge'])
                            <span class="product__badge">{{ $p['badge'] }}</span>
                        @endif
                    </div>
                    <div class="product__body">
                        <p class="product__cat">{{ $p['category'] }}</p>
                        <h3>{{ $p['name'] }}</h3>
                        <p class="product__desc">{{ $p['desc'] }}</p>
                        <div class="product__foot">
                            <strong>Rp {{ number_format($p['price'], 0, ',', '.') }}</strong>
                            <a href="{{ route('catalog', ['produk' => $p['slug']]) }}" class="btn btn--small">Lihat</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= INFO OUTLET ================= --}}
<section id="outlet" class="section">
    <div class="container outlet">
        <figure class="outlet__media">
            <img src="{{ asset('images/outlet-hero.jpg') }}" alt="Outlet {{ $outlet['name'] }}" loading="lazy" width="900" height="1010">
        </figure>

        <div class="outlet__info">
            <p class="eyebrow">Kunjungi outlet</p>
            <h2 class="h2">{{ $outlet['name'] }}</h2>
            <p class="lead">{{ $outlet['tagline'] }}</p>

            <dl class="outlet__list">
                <div>
                    <dt>Alamat</dt>
                    <dd>{{ $outlet['address'] }}</dd>
                </div>
                <div>
                    <dt>Jam buka</dt>
                    <dd>
                        @foreach ($outlet['hours'] as [$day, $time])
                            <span class="hours"><span>{{ $day }}</span><span>{{ $time }}</span></span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt>Kontak</dt>
                    <dd>{{ $outlet['phone'] }} &middot; {{ $outlet['instagram'] }}</dd>
                </div>
            </dl>

            <ul class="chips">
                @foreach ($outlet['features'] as $f)
                    <li>{{ $f }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/quiz.js') }}" defer></script>
@endpush
