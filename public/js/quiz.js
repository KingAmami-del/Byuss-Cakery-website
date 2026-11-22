// Kuis rekomendasi Byuss Cakery: "Kenalan dulu dengan penerimanya"
// Data produk datang dari HomeController lewat window.BYUSS_QUIZ_ITEMS.
(function () {
    var section = document.getElementById('kuis');
    if (!section) return;

    var resultSection = document.getElementById('kuis-hasil');
    var form = section.querySelector('[data-kq-form]');
    var errorEl = section.querySelector('[data-kq-error]');
    var resultEl = resultSection.querySelector('[data-kq-result]');
    var resultTitle = resultSection.querySelector('[data-kq-result-title]');
    var openBtn = document.querySelector('[data-quiz-open]');
    var items = window.BYUSS_QUIZ_ITEMS || [];
    var dialog = section.querySelector('[data-kq-dialog]');
    var lastFocus = null;

    /* ---------- Pop up: kuis & hasil ---------- */
    function openModal(sec) {
        var d = sec.querySelector('[data-kq-dialog]');
        sec.hidden = false;
        document.body.style.overflow = 'hidden';
        d.scrollTop = 0;
        var title = sec.querySelector('.kq__title');
        if (title) title.focus({ preventScroll: true });
    }

    function openQuiz() {
        lastFocus = document.activeElement;
        if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
        openModal(section);
    }

    function closeAll() {
        if (section.hidden && resultSection.hidden) return;
        section.hidden = true;
        resultSection.hidden = true;
        document.body.style.overflow = '';
        if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
        if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
    }

    if (openBtn) openBtn.addEventListener('click', openQuiz);
    if (location.hash === '#kuis') openQuiz();

    [section, resultSection].forEach(function (sec) {
        sec.querySelectorAll('[data-kq-close]').forEach(function (b) {
            b.addEventListener('click', closeAll);
        });
    });

    // Dari hasil kembali ke pertanyaan (jawaban tetap tersimpan)
    resultSection.querySelector('[data-kq-back]').addEventListener('click', function () {
        resultSection.hidden = true;
        openModal(section);
    });

    document.addEventListener('keydown', function (e) {
        var active = !resultSection.hidden ? resultSection : (!section.hidden ? section : null);
        if (!active) return;
        if (e.key === 'Escape') { closeAll(); return; }
        if (e.key !== 'Tab') return;

        // Jaga fokus tetap di dalam pop up
        var box = active.querySelector('[data-kq-dialog]');
        var f = Array.prototype.filter.call(
            box.querySelectorAll('a[href], button, input, .kq__title'),
            function (n) { return n.offsetParent !== null || n.type === 'radio' || n.type === 'checkbox'; }
        );
        if (!f.length) return;
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && (document.activeElement === first || !box.contains(document.activeElement))) {
            e.preventDefault(); last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault(); first.focus();
        }
    });

    /* ---------- "Bebas, semua boleh" bersifat eksklusif di pertanyaan 3 ---------- */
    form.addEventListener('change', function (e) {
        var input = e.target;
        if (input.name !== 'flavor') return;

        var boxes = form.querySelectorAll('input[name="flavor"]');
        if (input.checked && input.hasAttribute('data-exclusive')) {
            boxes.forEach(function (b) { if (b !== input) b.checked = false; });
        } else if (input.checked) {
            boxes.forEach(function (b) { if (b.hasAttribute('data-exclusive')) b.checked = false; });
        }
        errorEl.hidden = true;
    });

    form.addEventListener('change', function () { errorEl.hidden = true; });

    /* ---------- Helper ---------- */
    function checked(name) {
        return form.querySelector('input[name="' + name + '"]:checked');
    }

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function rupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function has(list, value) {
        return Array.isArray(list) && list.indexOf(value) !== -1;
    }

    /* ---------- Penilaian ---------- */
    function score(item, answer) {
        var points = 0;
        var why = [];

        if (has(item.mood, answer.mood.value)) {
            points += 3;
            why.push('cocok untuk yang ' + answer.mood.label.toLowerCase());
        }

        var taste = item.type === 'drink' ? answer.drink : answer.food;
        if (taste.value !== 'bebas' && has(item.taste, taste.value)) {
            points += 2;
            why.push('rasa ' + taste.label.toLowerCase());
        }

        answer.flavors.forEach(function (f) {
            if (f.value !== 'bebas' && has(item.flavors, f.value)) {
                points += 2;
                why.push(f.label.toLowerCase());
            }
        });

        if (item.featured) points += 0.25; // seri: utamakan produk unggulan
        return { item: item, points: points, why: why };
    }

    function ranked(type, answer) {
        return items
            .map(function (it, order) { var s = score(it, answer); s.order = order; return s; })
            .filter(function (s) { return s.item.type === type; })
            .sort(function (a, b) { return b.points - a.points || a.order - b.order; });
    }

    // Dessert: pilihan kedua diutamakan dari kelompok menu yang berbeda supaya bervariasi
    function pickFoods(answer) {
        var list = ranked('food', answer);
        var first = list[0];
        if (!first) return [];
        var second = list.slice(1).filter(function (s) { return s.item.group !== first.item.group; })[0] || list[1];
        return second ? [first, second] : [first];
    }

    /* ---------- Tampilkan hasil ---------- */
    function photo(item) {
        var box = el('div', 'kq-card__photo');
        if (item.image) {
            var img = el('img');
            img.src = item.image;
            img.alt = item.name;
            img.loading = 'lazy';
            box.appendChild(img);
        } else {
            box.classList.add('is-empty');
            box.innerHTML = '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10.5" r="1.6"/><path d="m21 16-5-5-8 8"/></svg>';
            box.appendChild(el('span', null, 'Foto segera hadir'));
        }
        return box;
    }

    function card(s, tag) {
        var item = s.item;
        var c = el('article', 'kq-card');
        c.appendChild(photo(item));
        c.appendChild(el('span', 'kq-card__tag', tag));
        c.appendChild(el('p', 'kq-card__group', item.group));
        c.appendChild(el('h4', null, item.name));
        c.appendChild(el('p', 'kq-card__desc', item.desc));
        c.appendChild(el('p', 'kq-card__why', s.why.length ? 'Karena ' + s.why.join(', ') : 'Pilihan favorit dari dapur kami'));

        var foot = el('div', 'kq-card__foot');
        var priceEl = el('strong', 'kq-card__price');
        var picked = null; // varian terpilih (minuman: ice / hot)

        function currentPrice() {
            return item.type === 'drink'
                ? (picked ? item[picked] : null)
                : item.price;
        }
        function paint() {
            var p = currentPrice();
            priceEl.textContent = p ? rupiah(p) : '';
        }

        if (item.type === 'drink') {
            var variants = ['ice', 'hot'].filter(function (v) { return item[v]; });
            picked = variants[0];
            if (variants.length > 1) {
                var seg = el('div', 'kq-seg');
                seg.setAttribute('role', 'radiogroup');
                seg.setAttribute('aria-label', 'Pilih suhu minuman');
                variants.forEach(function (v) {
                    var b = el('button', 'kq-seg__btn' + (v === picked ? ' is-on' : ''), v === 'ice' ? 'Ice' : 'Hot');
                    b.type = 'button';
                    b.setAttribute('role', 'radio');
                    b.setAttribute('aria-checked', v === picked ? 'true' : 'false');
                    b.addEventListener('click', function () {
                        picked = v;
                        seg.querySelectorAll('.kq-seg__btn').forEach(function (x) {
                            var on = x === b;
                            x.classList.toggle('is-on', on);
                            x.setAttribute('aria-checked', on ? 'true' : 'false');
                        });
                        paint();
                    });
                    seg.appendChild(b);
                });
                c.appendChild(seg);
            } else {
                c.appendChild(el('p', 'kq-card__only', 'Hanya tersedia ' + (picked === 'ice' ? 'Ice' : 'Hot')));
            }
        }
        paint();

        var addBtn = el('button', 'btn btn--primary btn--small kq-card__add');
        addBtn.type = 'button';
        var ICON = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h13l-1.5 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.6L5.5 4H3"/><circle cx="10" cy="20.5" r="1"/><circle cx="16" cy="20.5" r="1"/></svg>';
        function label(done) { addBtn.innerHTML = ICON + '<span>' + (done ? 'Ditambahkan' : 'Tambah ke keranjang') + '</span>'; }
        label(false);
        addBtn.setAttribute('aria-label', 'Tambah ' + item.name + ' ke keranjang');

        function add() {
            if (!window.ByussCart) return;
            window.ByussCart.add({
                slug: item.slug,
                name: item.name,
                group: item.group,
                image: item.image || null,
                description: item.desc,
                variants: { ice: item.ice || null, hot: item.hot || null },
                variant: picked,
                price: currentPrice()
            });
        }
        var timer;
        function flash() {
            label(true);
            addBtn.classList.add('is-done');
            clearTimeout(timer);
            timer = setTimeout(function () {
                label(false);
                addBtn.classList.remove('is-done');
            }, 1600);
        }
        addBtn.addEventListener('click', function () { add(); flash(); });

        if (priceEl.textContent) foot.appendChild(priceEl);
        foot.appendChild(addBtn);
        c.appendChild(foot);
        return c;
    }

    function showResult(answer) {
        var foods = pickFoods(answer);
        var drinks = ranked('drink', answer).slice(0, 1);

        resultTitle.textContent = 'Pilihan manis untuk ' + answer.mood.label.toLowerCase() + ' ✿';
        resultEl.innerHTML = '';

        var grid = el('div', 'kq__grid');
        foods.forEach(function (s, i) { grid.appendChild(card(s, i === 0 ? 'Paling cocok' : 'Pilihan kedua')); });
        drinks.forEach(function (s) { grid.appendChild(card(s, 'Teman minumnya')); });
        resultEl.appendChild(grid);

        section.hidden = true;
        openModal(resultSection);
    }

    /* ---------- Submit & reset ---------- */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var mood = checked('mood');
        var food = checked('foodTaste');
        var drink = checked('drinkTaste');
        var flavors = Array.prototype.slice.call(form.querySelectorAll('input[name="flavor"]:checked'));

        var missing = [];
        if (!mood) missing.push('1');
        if (!food || !drink) missing.push('2');
        if (!flavors.length) missing.push('3');

        if (missing.length) {
            errorEl.textContent = 'Lengkapi dulu pertanyaan nomor ' + missing.join(' dan ') + ' ya.';
            errorEl.hidden = false;
            var first = form.querySelector('[data-q="' + missing[0] + '"]');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        errorEl.hidden = true;
        showResult({
            mood: { value: mood.value, label: mood.dataset.label },
            food: { value: food.value, label: food.dataset.label },
            drink: { value: drink.value, label: drink.dataset.label },
            flavors: flavors.map(function (f) { return { value: f.value, label: f.dataset.label }; })
        });
    });

    section.querySelector('[data-kq-reset]').addEventListener('click', function () {
        form.reset();
        errorEl.hidden = true;
        resultEl.innerHTML = '';
        dialog.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
