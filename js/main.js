// ============================================================
// ShopWave — Main JavaScript
// ============================================================

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            flash.style.transition = 'all 0.4s ease';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }

    // Quantity controls
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.qty-input');
            let val = parseInt(input.value) || 1;
            if (btn.dataset.action === 'inc') val = Math.min(val + 1, 99);
            if (btn.dataset.action === 'dec') val = Math.max(val - 1, 1);
            input.value = val;
        });
    });

    // Confirm deletes
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
        });
    });

    // Image preview on admin upload
    const imgInput = document.getElementById('product_image_file');
    const imgPreview = document.getElementById('image_preview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', () => {
            const file = imgInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { imgPreview.src = e.target.result; imgPreview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Cart quantity update via AJAX
function updateCartQty(productId, qty) {
    // Get BASE_URL from window object (will be set in header.php)
    const baseUrl = window.BASE_URL || '';
    fetch(baseUrl + '/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&product_id=${productId}&quantity=${qty}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            if (data.item_subtotal !== undefined) {
                const subEl = document.getElementById(`subtotal-${productId}`);
                if (subEl) subEl.textContent = '$' + parseFloat(data.item_subtotal).toFixed(2);
            }
            const totalEl = document.getElementById('cart-total');
            if (totalEl) totalEl.textContent = '$' + parseFloat(data.total).toFixed(2);
        }
    })
    .catch(() => window.location.reload());
}
