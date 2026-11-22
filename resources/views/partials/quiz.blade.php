@php($q = config('byuss_quiz.questions'))

<section id="kuis" class="kq" role="dialog" aria-modal="true" aria-labelledby="kq-title" hidden>
    <div class="kq__backdrop" data-kq-close></div>

    <div class="kq__dialog" data-kq-dialog>
        <button type="button" class="kq__close" data-kq-close aria-label="Tutup kuis">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <header class="kq__head">
            <h2 class="kq__title" id="kq-title" tabindex="-1">Kenalan dulu dengan penerimanya.</h2>
            <p class="kq__sub">Tiga pertanyaan singkat supaya rekomendasinya terasa personal.</p>
        </header>

        <form class="kq__form" data-kq-form novalidate>

            {{-- 1. Mood penerima --}}
            <fieldset class="kq__q" data-q="1">
                <legend><span>1.</span> {{ $q['mood']['title'] }}</legend>
                <div class="kq__cards">
                    @foreach ($q['mood']['options'] as $o)
                        <label class="kq-pick">
                            <span class="kq-pick__text">
                                <strong>{{ $o['label'] }}</strong>
                                <small>{{ $o['desc'] }}</small>
                            </span>
                            <input type="radio" name="mood" value="{{ $o['value'] }}" data-label="{{ $o['label'] }}">
                            <span class="kq-pick__dot" aria-hidden="true"></span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- 2. Rasa dominan --}}
            <fieldset class="kq__q" data-q="2">
                <legend><span>2.</span> {{ $q['taste']['title'] }}</legend>
                <div class="kq__panels">
                    <div class="kq-panel" role="group" aria-labelledby="kq-food">
                        <p class="kq-panel__label" id="kq-food">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18h18"/><path d="M5 18a7 7 0 0 1 14 0"/><path d="M12 7V5"/></svg>
                            {{ $q['taste']['food']['label'] }}
                        </p>
                        <div class="kq__chips">
                            @foreach ($q['taste']['food']['options'] as $o)
                                <label class="kq-chip">
                                    <input type="radio" name="foodTaste" value="{{ $o['value'] }}" data-label="{{ $o['label'] }}">
                                    <span>{{ $o['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="kq-panel" role="group" aria-labelledby="kq-drink">
                        <p class="kq-panel__label" id="kq-drink">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 9h11v5a5 5 0 0 1-5 5h-1a5 5 0 0 1-5-5V9Z"/><path d="M16 10h2a2 2 0 0 1 0 4h-2"/></svg>
                            {{ $q['taste']['drink']['label'] }}
                        </p>
                        <div class="kq__chips">
                            @foreach ($q['taste']['drink']['options'] as $o)
                                <label class="kq-chip">
                                    <input type="radio" name="drinkTaste" value="{{ $o['value'] }}" data-label="{{ $o['label'] }}">
                                    <span>{{ $o['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- 3. Rasa favorit (boleh banyak) --}}
            <fieldset class="kq__q" data-q="3">
                <legend><span>3.</span> {{ $q['flavor']['title'] }}</legend>
                <p class="kq__hint">{{ $q['flavor']['hint'] }}</p>
                <div class="kq__chips">
                    @foreach ($q['flavor']['options'] as $o)
                        <label class="kq-chip">
                            <input type="checkbox" name="flavor" value="{{ $o['value'] }}" data-label="{{ $o['label'] }}" @if (!empty($o['exclusive'])) data-exclusive @endif>
                            <span>{{ $o['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <p class="kq__error" data-kq-error role="alert" hidden></p>

            <div class="kq__actions">
                <button type="submit" class="btn btn--primary">Lihat rekomendasi <span aria-hidden="true">&rarr;</span></button>
                <button type="button" class="btn btn--ghost" data-kq-reset>Atur ulang</button>
            </div>
        </form>

    </div>
</section>

{{-- ================= POP UP HASIL REKOMENDASI ================= --}}
<section id="kuis-hasil" class="kq kq--result" role="dialog" aria-modal="true" aria-labelledby="kq-hasil-title" hidden>
    <div class="kq__backdrop" data-kq-close></div>

    <div class="kq__dialog" data-kq-dialog>
        <button type="button" class="kq__close" data-kq-close aria-label="Tutup hasil rekomendasi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <header class="kq__head">
            <h2 class="kq__title" id="kq-hasil-title" tabindex="-1" data-kq-result-title>Pilihan manis untuk kamu ✿</h2>
            <p class="kq__sub">Kami pilihkan dua dessert dan satu minuman yang paling cocok untuk penerima kejutanmu.</p>
        </header>

        <div class="kq__result" data-kq-result></div>

        <div class="kq__actions kq__actions--result">
            <button type="button" class="btn btn--primary" data-cart-open>Keranjang (<span data-cart-count>0</span>)</button>
            <button type="button" class="btn btn--ghost" data-kq-back>&larr; Ubah jawaban</button>
            <a href="{{ route('catalog') }}" class="btn btn--ghost">Lihat katalog <span aria-hidden="true">&rarr;</span></a>
        </div>
    </div>
</section>

@include('partials.cart')

@push('scripts')
    <script>
        window.BYUSS_QUIZ_ITEMS = @json($quizItems);
    </script>
@endpush
