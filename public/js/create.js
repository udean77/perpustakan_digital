const priceInput = document.getElementById('price');
const priceWarning = document.getElementById('priceWarning');
const maxPrice = 99999999.99;

document.querySelector('form').addEventListener('submit', function (e) {
    let hasError = false;

    // Cek book type sudah dipilih
    if (!bookTypeSelect.value) {
        warning.classList.remove('d-none');
        bookTypeSelect.focus();
        hasError = true;
    } else {
        warning.classList.add('d-none');
    }

    // Cek harga tidak melebihi maxPrice
    const priceValue = parseFloat(priceInput.value);
    if (isNaN(priceValue) || priceValue > maxPrice) {
        priceWarning.classList.remove('d-none');
        priceInput.focus();
        hasError = true;
    } else {
        priceWarning.classList.add('d-none');
    }

    if (hasError) {
        e.preventDefault();
    }
});
