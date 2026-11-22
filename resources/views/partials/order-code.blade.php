<section class="order-code-card" aria-label="Kode pesanan">
    <div class="order-code-card__row">
        <div>
            <p class="eyebrow">Kode pesanan</p>
            <strong class="order-code-card__value">{{ $orderNumber }}</strong>
        </div>
        <button class="btn btn--ghost order-code-card__copy" type="button" data-copy-order-code="{{ $orderNumber }}">
            Salin kode
        </button>
    </div>
    <p class="order-code-card__hint">Simpan kode ini untuk melihat status pesanan.</p>
    <p class="order-code-card__feedback" data-copy-feedback role="status" aria-live="polite"></p>
</section>
