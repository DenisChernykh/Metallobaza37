export function initCheckProductCart() {
    initCheck();
    // getCartTotalData();
}

function getCartTotalData(){
    fetch('/wp-content/plugins/VG_Catalog/router.php?route=cart-get-total', {
        method: 'GET',
    })
        .then(r => r.json())
        .then(data => {
           
        })
        .catch(err => console.error('Fetch error:', err));
}

function initCheck() {
    fetch('/wp-content/plugins/VG_Catalog/router.php?route=cart-get-list&limit=0', {
        method: 'GET',
    })
        .then(r => r.json())
        .then(data => {
            updateCartUI(data.data);
        })
        .catch(err => console.error('Fetch error:', err));
}

function updateCartUI(cartData) {
    if (!cartData?.cart) return;

    updateCartCounter(cartData.cart.length);

    cartData.cart.forEach(item => {
        const postId = item.product.post_id;
        const cartId = item.id;
        const quantity = item.quantity;

        const productElements = document.querySelectorAll(`.product-list-item__quantity[data-product-id="${postId}"]`);

        productElements.forEach(productEl => {
            const addBtn = productEl.querySelector('.btn--add-to-cart');
            if (addBtn) addBtn.classList.add('hidden');

            const qtyBlock = productEl.querySelector('.quantity-block');
            if (qtyBlock) qtyBlock.classList.remove('hidden');

            const qtyInput = productEl.querySelector('[data-qty-input]');
            if (qtyInput) qtyInput.value = quantity;

            productEl.dataset.cartId = cartId;
        });
    });
}

/**
 * Функция для обновления счетчика корзины
 * @param {number} count - количество товаров в корзине
 */
function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-icon__quantity');

    if (!cartCounter) return;

    cartCounter.textContent = count;

    if (count > 0) {
        cartCounter.classList.remove('hidden');
    }
    else {
        cartCounter.classList.add('hidden');
    }
}