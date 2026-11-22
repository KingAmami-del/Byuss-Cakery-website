(function () {
    var form = document.querySelector('[data-checkout-form]');
    if (!form) return;

    var list = document.querySelector('[data-checkout-items]');
    var total = document.querySelector('[data-checkout-total]');
    var emptyMessage = document.querySelector('[data-checkout-empty]');
    var submit = document.querySelector('[data-checkout-submit]');
    var items = [];

    try {
        items = JSON.parse(localStorage.getItem('byuss_cart_v1')) || [];
    } catch (error) {
        items = [];
    }

    if (!Array.isArray(items)) items = [];

    function rupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function hiddenInput(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    list.innerHTML = '';
    var subtotal = 0;
    items.forEach(function (item, index) {
        if (!item || typeof item.slug !== 'string') return;

        var quantity = Number(item.qty);
        if (!Number.isInteger(quantity) || quantity < 1) return;
        var price = Number(item.price) || 0;
        var variant = item.variant === 'ice' || item.variant === 'hot' ? item.variant : null;
        subtotal += price * quantity;

        var row = document.createElement('li');
        var name = document.createElement('span');
        name.textContent = (item.name || item.slug) + (variant ? ' · ' + (variant === 'ice' ? 'Ice' : 'Hot') : '') + ' × ' + quantity;
        var amount = document.createElement('strong');
        amount.textContent = rupiah(price * quantity);
        row.appendChild(name);
        row.appendChild(amount);
        list.appendChild(row);

        hiddenInput('items[' + index + '][slug]', item.slug);
        hiddenInput('items[' + index + '][quantity]', quantity);
        if (variant) hiddenInput('items[' + index + '][variant]', variant);
    });

    total.textContent = rupiah(subtotal);
    var hasItems = list.children.length > 0;
    emptyMessage.hidden = hasItems;
    submit.disabled = !hasItems;

    form.addEventListener('submit', function (event) {
        if (list.children.length === 0) {
            event.preventDefault();
            emptyMessage.hidden = false;
        }
    });
})();
