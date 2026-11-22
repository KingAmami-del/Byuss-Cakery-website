document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-order-code]');
    if (!button) {
        return;
    }

    const feedback = button.parentElement.parentElement.querySelector('[data-copy-feedback]');
    const code = button.dataset.copyOrderCode;

    try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(code);
        } else {
            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = code;
            temporaryInput.setAttribute('readonly', '');
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.left = '-9999px';
            document.body.appendChild(temporaryInput);
            temporaryInput.select();

            const copied = document.execCommand('copy');
            temporaryInput.remove();

            if (!copied) {
                throw new Error('Clipboard copy was not successful.');
            }
        }

        feedback.textContent = 'Kode pesanan berhasil disalin.';
    } catch {
        feedback.textContent = 'Kode tidak berhasil disalin. Silakan salin kode secara manual.';
    }
});
