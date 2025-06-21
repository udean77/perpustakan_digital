// Wishlist AJAX toggle
const wishlistForm = document.getElementById('wishlist-form');
if (wishlistForm) {
    wishlistForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('wishlist-btn');
        const icon = document.getElementById('wishlist-icon');
        const text = document.getElementById('wishlist-text');
        const url = this.action;
        const token = this.querySelector('input[name="_token"]').value;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if(data.in_wishlist) {
                icon.classList.replace('bi-heart', 'bi-heart-fill');
                icon.classList.add('text-danger');
                text.textContent = 'Sudah di Wishlist';
            } else {
                icon.classList.replace('bi-heart-fill', 'bi-heart');
                icon.classList.remove('text-danger');
                text.textContent = 'Wishlist';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengubah wishlist, silakan coba lagi.');
        });
    });
}

// Kuantitas
function changeQuantity(change) {
    const qtyInput = document.getElementById('quantity');
    const buyNowInput = document.getElementById('buy-now-quantity');
    let qty = parseInt(qtyInput.value);
    const max = parseInt(qtyInput.max);

    qty += change;
    if (qty < 1) qty = 1;
    if (qty > max) qty = max;

    qtyInput.value = qty;
    if (buyNowInput) buyNowInput.value = qty;
}

// Modal Laporan
ddocument.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reportForm');
    const reasonSelect = document.getElementById('reportReason');
    const otherReasonDiv = document.getElementById('otherReasonDiv');
    const otherReasonInput = document.getElementById('otherReason');
    const reportMsg = document.getElementById('reportMsg');

    // Tampilkan input alasan lainnya jika dipilih
    reasonSelect.addEventListener('change', function () {
        if (this.value === 'other') {
            otherReasonDiv.classList.remove('d-none');
        } else {
            otherReasonDiv.classList.add('d-none');
            otherReasonInput.value = '';
        }
    });

    // Submit form pakai fetch
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const reason = reasonSelect.value;
        const otherReason = otherReasonInput.value;

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reason: reason,
                other_reason: otherReason
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengirim laporan');
            }
            return response.json();
        })
        .then(data => {
            reportMsg.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            form.reset();
            otherReasonDiv.classList.add('d-none');
        })
        .catch(error => {
            reportMsg.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        });
    });
});
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

fetch(`/books/${bookId}/complaint`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': token,
  },
  body: JSON.stringify({
    reason: selectedReason,
    other_reason: otherReasonInput
  })
})
.then(res => res.json())
.then(data => {
  alert(data.message);
})
.catch(err => console.error(err));

