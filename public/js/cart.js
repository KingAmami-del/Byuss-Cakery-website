// Keranjang sederhana Byuss Cakery (tersimpan di browser lewat localStorage).
// API untuk dihubungkan ke keranjang/checkout asli:
//   window.ByussCart.add({slug, name, group, image, description, variants, variant, price})
//   window.ByussCart.items()  -> daftar item { id, slug, name, group, image, description, variants, variant, price, qty }
//   document.addEventListener('byuss:cart-changed', e => e.detail.items)
//   Isi window.BYUSS_CHECKOUT_URL agar tombol "Lanjut pesan" muncul dan mengarah ke sana.
(function () {
    var KEY = 'byuss_cart_v1';
    var list = [];

    try { list = JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { list = []; }
    if (!Array.isArray(list)) list = [];

    var modal = document.getElementById('keranjang');
    if (!modal) return;
    var dialog = modal.querySelector('[data-cart-dialog]');
    var listEl = modal.querySelector('[data-cart-list]');
    var emptyEl = modal.querySelector('[data-cart-empty]');
    var footEl = modal.querySelector('[data-cart-foot]');
    var totalEl = modal.querySelector('[data-cart-total]');
    var noteEl = modal.querySelector('[data-cart-note]');
    var summaryEl = modal.querySelector('[data-cart-summary]');
    var checkoutBtn = modal.querySelector('[data-cart-checkout]');
    var fab = document.querySelector('.cart-fab');
    var prevOverflow = '';
    var lastFocus = null;

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) { /* abaikan */ }
    }
    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text !== undefined) n.textContent = text;
        return n;
    }
    function rupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    function count() { return list.reduce(function (a, i) { return a + i.qty; }, 0); }

    function render() {
        var n = count();
        document.querySelectorAll('[data-cart-count]').forEach(function (x) { x.textContent = n; });
        if (fab) fab.hidden = n === 0;

        listEl.innerHTML = '';
        emptyEl.hidden = n !== 0;
        footEl.hidden = n === 0;
        summaryEl.textContent = n ? n + ' item siap dipesan.' : '';

        list.forEach(function (it) {
            var li = el('li', 'cart__item');

            var thumb = el('div', 'cart__thumb');
            if (it.image) {
                var img = el('img');
                img.src = it.image; img.alt = ''; thumb.appendChild(img);
            }
            li.appendChild(thumb);

            var info = el('div', 'cart__info');
            info.appendChild(el('strong', null, it.name));
            var meta = [it.group];
            if (it.variant) meta.push(it.variant === 'ice' ? 'Ice' : 'Hot');
            info.appendChild(el('small', null, meta.join(' · ')));
            if (it.description) info.appendChild(el('p', 'cart__description', it.description));

            var variants = it.variants || {};
            var availableVariants = Object.keys(variants).filter(function (variant) { return variants[variant]; });
            if (availableVariants.length > 1) {
                var variantLabel = el('label', 'cart__variant', 'Suhu');
                var variantSelect = el('select');
                variantSelect.setAttribute('aria-label', 'Pilih suhu ' + it.name);
                availableVariants.forEach(function (variant) {
                    var option = el('option', null, variant === 'ice' ? 'Ice' : 'Hot');
                    option.value = variant;
                    option.selected = variant === it.variant;
                    variantSelect.appendChild(option);
                });
                variantSelect.addEventListener('change', function () { setVariant(it.id, variantSelect.value); });
                variantLabel.appendChild(variantSelect);
                info.appendChild(variantLabel);
            }

            info.appendChild(el('span', 'cart__price', it.price ? rupiah(it.price * it.qty) : 'Harga menyusul'));
            li.appendChild(info);

            var qty = el('div', 'cart__qty');
            var minus = el('button', null, '−'); minus.type = 'button';
            minus.setAttribute('aria-label', 'Kurangi ' + it.name);
            var plus = el('button', null, '+'); plus.type = 'button';
            plus.setAttribute('aria-label', 'Tambah ' + it.name);
            minus.addEventListener('click', function () { setQty(it.id, it.qty - 1); });
            plus.addEventListener('click', function () { setQty(it.id, it.qty + 1); });
            qty.appendChild(minus); qty.appendChild(el('span', null, String(it.qty))); qty.appendChild(plus);
            li.appendChild(qty);

            listEl.appendChild(li);
        });

        var priced = list.filter(function (i) { return i.price; });
        var total = priced.reduce(function (a, i) { return a + i.price * i.qty; }, 0);
        var unpriced = list.length - priced.length;
        totalEl.textContent = rupiah(total);
        noteEl.hidden = !unpriced;
        noteEl.textContent = unpriced ? 'Belum termasuk ' + unpriced + ' item yang harganya belum tersedia.' : '';
    }

    function changed() {
        save(); render();
        document.dispatchEvent(new CustomEvent('byuss:cart-changed', { detail: { items: list.slice() } }));
    }

    function add(p) {
        var id = p.slug + (p.variant ? ':' + p.variant : '');
        var found = list.filter(function (i) { return i.id === id; })[0];
        if (found) {
            found.qty += 1;
            found.name = p.name;
            found.group = p.group;
            found.image = p.image || found.image || null;
            found.description = p.description || found.description || '';
            found.variants = p.variants || found.variants || {};
            found.price = p.price || found.price || null;
        } else {
            list.push({
                id: id,
                slug: p.slug,
                name: p.name,
                group: p.group,
                image: p.image || null,
                description: p.description || '',
                variants: p.variants || {},
                variant: p.variant || null,
                price: p.price || null,
                qty: 1
            });
        }
        changed();
    }
    function setVariant(id, variant) {
        var item = list.filter(function (i) { return i.id === id; })[0];
        if (!item || !item.variants || !item.variants[variant]) return;

        var newId = item.slug + ':' + variant;
        var existing = list.filter(function (i) { return i.id === newId; })[0];
        if (existing && existing !== item) {
            existing.qty += item.qty;
            existing.name = item.name;
            existing.group = item.group;
            existing.image = item.image || existing.image || null;
            existing.description = item.description || existing.description || '';
            existing.variant = variant;
            existing.price = item.variants[variant];
            existing.variants = item.variants;
            list = list.filter(function (i) { return i !== item; });
        } else {
            item.id = newId;
            item.variant = variant;
            item.price = item.variants[variant];
        }
        changed();
    }
    function setQty(id, q) {
        list = list.map(function (i) { if (i.id === id) i.qty = q; return i; }).filter(function (i) { return i.qty > 0; });
        changed();
    }

    /* ---------- Buka / tutup ---------- */
    function open() {
        lastFocus = document.activeElement;
        prevOverflow = document.body.style.overflow;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        dialog.scrollTop = 0;
        modal.querySelector('.kq__title').focus({ preventScroll: true });
    }
    function close() {
        if (modal.hidden) return;
        modal.hidden = true;
        document.body.style.overflow = prevOverflow;
        if (lastFocus && lastFocus.focus && document.contains(lastFocus)) lastFocus.focus({ preventScroll: true });
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-cart-open]');
        if (trigger) {
            e.preventDefault();
            open();
        }
    });
    modal.querySelectorAll('[data-cart-close]').forEach(function (b) { b.addEventListener('click', close); });

    // Capture: Esc menutup keranjang saja, tidak ikut menutup pop up kuis di bawahnya
    document.addEventListener('keydown', function (e) {
        if (modal.hidden) return;
        if (e.key === 'Escape') { e.stopImmediatePropagation(); close(); return; }
        if (e.key !== 'Tab') return;
        var f = dialog.querySelectorAll('button:not([hidden]), a[href], .kq__title');
        var vis = Array.prototype.filter.call(f, function (n) { return n.offsetParent !== null; });
        if (!vis.length) return;
        var first = vis[0], last = vis[vis.length - 1];
        e.stopImmediatePropagation();
        if (e.shiftKey && (document.activeElement === first || !dialog.contains(document.activeElement))) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }, true);

    modal.querySelector('[data-cart-clear]').addEventListener('click', function () { list = []; changed(); });

    checkoutBtn.hidden = !window.BYUSS_CHECKOUT_URL;
    checkoutBtn.addEventListener('click', function () {
        var ev = new CustomEvent('byuss:checkout', { cancelable: true, detail: { items: list.slice() } });
        if (document.dispatchEvent(ev) && window.BYUSS_CHECKOUT_URL) location.href = window.BYUSS_CHECKOUT_URL;
    });

    window.ByussCart = {
        add: add,
        setQty: setQty,
        items: function () { return list.slice(); },
        count: count,
        open: open,
        close: close
    };
    render();
})();
