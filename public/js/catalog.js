// Katalog Byuss Cakery: filter jenis & kategori, pencarian, pilihan Ice/Hot, tambah ke keranjang.
(function () {
    var root = document.querySelector('[data-catalog]');
    if (!root) return;

    var tabs = root.querySelectorAll('[data-cat-type]');
    var chips = root.querySelectorAll('[data-cat-group]');
    var search = root.querySelector('[data-cat-search]');
    var countEl = root.querySelector('[data-cat-count]');
    var emptyEl = root.querySelector('[data-cat-empty]');
    var sections = root.querySelectorAll('[data-group-section]');
    var cards = root.querySelectorAll('.pcard');

    var state = { type: 'all', group: 'all', q: '' };

    function rupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

    /* ---------- Filter ---------- */
    function apply() {
        var q = state.q.trim().toLowerCase();
        var shown = 0;

        cards.forEach(function (c) {
            var ok = (state.type === 'all' || c.dataset.type === state.type) &&
                     (state.group === 'all' || c.dataset.group === state.group) &&
                     (!q || c.dataset.search.indexOf(q) !== -1);
            c.hidden = !ok;
            if (ok) shown++;
        });

        sections.forEach(function (s) {
            s.hidden = !s.querySelector('.pcard:not([hidden])');
        });

        // Chip kategori hanya yang sesuai jenis terpilih
        chips.forEach(function (ch) {
            var f = ch.dataset.for;
            ch.hidden = !(f === 'all' || state.type === 'all' || f === state.type);
            var on = ch.dataset.catGroup === state.group;
            ch.classList.toggle('is-on', on);
        });

        tabs.forEach(function (t) {
            var on = t.dataset.catType === state.type;
            t.classList.toggle('is-on', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        countEl.textContent = shown ? 'Menampilkan ' + shown + ' menu' : 'Tidak ada menu yang cocok';
        emptyEl.hidden = shown !== 0;
    }

    function reset() {
        state = { type: 'all', group: 'all', q: '' };
        search.value = '';
        apply();
    }

    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            state.type = t.dataset.catType;
            // Kategori terpilih dibatalkan bila tidak cocok dengan jenis baru
            var cur = null;
            chips.forEach(function (chip) {
                if (chip.dataset.catGroup === state.group) cur = chip;
            });
            if (cur && cur.dataset.for !== 'all' && state.type !== 'all' && cur.dataset.for !== state.type) state.group = 'all';
            apply();
        });
    });
    chips.forEach(function (ch) {
        ch.addEventListener('click', function () { state.group = ch.dataset.catGroup; apply(); });
    });
    search.addEventListener('input', function () { state.q = search.value; apply(); });
    root.querySelector('[data-cat-reset]').addEventListener('click', reset);

    /* ---------- Ice / Hot & keranjang ---------- */
    cards.forEach(function (card) {
        var item = JSON.parse(card.dataset.item);
        var addBtn = card.querySelector('[data-add]');
        var priceEl = card.querySelector('[data-price]');
        var picked = addBtn.dataset.variantNow || null;
        var timer;

        card.querySelectorAll('[data-variant]').forEach(function (b) {
            b.addEventListener('click', function () {
                picked = b.dataset.variant;
                card.querySelectorAll('[data-variant]').forEach(function (x) {
                    var on = x === b;
                    x.classList.toggle('is-on', on);
                    x.setAttribute('aria-checked', on ? 'true' : 'false');
                });
                if (priceEl) priceEl.textContent = rupiah(item[picked]);
            });
        });

        addBtn.addEventListener('click', function () {
            if (!window.ByussCart) return;
            window.ByussCart.add({
                slug: item.slug,
                name: item.name,
                group: item.group,
                image: item.image || null,
                description: item.desc,
                variants: { ice: item.ice || null, hot: item.hot || null },
                variant: picked,
                price: picked ? item[picked] : item.price
            });
            var label = addBtn.querySelector('span');
            label.textContent = 'Ditambahkan';
            addBtn.classList.add('is-done');
            clearTimeout(timer);
            timer = setTimeout(function () {
                label.textContent = 'Tambah ke keranjang';
                addBtn.classList.remove('is-done');
            }, 1600);
        });
    });

    /* ---------- Tautan langsung: /katalog?produk=slug ---------- */
    var slug = new URLSearchParams(location.search).get('produk');
    if (slug) {
        var target = document.getElementById('p-' + slug);
        if (target) {
            target.classList.add('is-hl');
            target.scrollIntoView({ block: 'center' });
        }
    }

    apply();
})();
