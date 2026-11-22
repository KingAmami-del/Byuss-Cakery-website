{{-- ================= KERANJANG (dipakai di beranda & katalog) ================= --}}
<button type="button" class="cart-fab" data-cart-open aria-label="Buka keranjang" hidden>
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h13l-1.5 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.6L5.5 4H3"/><circle cx="10" cy="20.5" r="1"/><circle cx="16" cy="20.5" r="1"/></svg>
    <span class="cart-fab__count" data-cart-count>0</span>
</button>

<section id="keranjang" class="kq kq--cart" role="dialog" aria-modal="true" aria-labelledby="kq-cart-title" hidden>
    <div class="kq__backdrop" data-cart-close></div>

    <div class="kq__dialog" data-cart-dialog>
        <button type="button" class="kq__close" data-cart-close aria-label="Tutup keranjang">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <header class="kq__head">
            <h2 class="kq__title" id="kq-cart-title" tabindex="-1">Keranjangmu</h2>
            <p class="kq__sub" data-cart-summary></p>
        </header>

        <p class="cart__empty" data-cart-empty>Keranjang masih kosong. Yuk temukan pilihan manismu dulu.</p>
        <ul class="cart__list" data-cart-list></ul>

        <div class="cart__foot" data-cart-foot>
            <div class="cart__total"><span>Total</span><strong data-cart-total>Rp 0</strong></div>
            <p class="cart__note" data-cart-note hidden></p>
            <div class="kq__actions">
                <button type="button" class="btn btn--primary" data-cart-checkout>Lanjut pesan</button>
                <button type="button" class="btn btn--ghost" data-cart-clear>Kosongkan</button>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script src="{{ asset('js/cart.js') }}" defer></script>
@endpush
