export function initCheckoutPage() {
    const checkoutInput = document.querySelector('input[name="page_checkout"][value="1"]');
    if (checkoutInput) {
        getCartTotalData();
    }
}

function getCartTotalData(){
    fetch('/wp-content/plugins/VG_Catalog/router.php?route=cart-get-total', {
        method: 'GET',
    })
        .then(r => r.json())
        .then(data => {
    

            if (data.success && data.data) {
                updateCartCount(data.data.count_products);
                updateDiscountDisplay(data.data.discount_amount);
                updateItogoPrice(data.data.price_with_discount);
                updateProductsPrice(data.data.full_price, data.data.count_products);
            }
        })
        .catch(err => console.error('Fetch error:', err));
}

function updateItogoPrice(itogoPrice){
    const discountRowTotal = document.querySelector('.cart-summary__row--total');
    const discountValueTotal = discountRowTotal ? discountRowTotal.querySelector('.cart-summary__price--big-price') : null;

    if (!discountRowTotal || !discountValueTotal) return;
    if (itogoPrice > 0) {
        const formattedAmount = formatCurrency(itogoPrice);
        discountValueTotal.textContent = `${formattedAmount} ₽`;
        discountRowTotal.classList.remove('hidden');
    }
}
function updateProductsPrice(productPrice, productsCount){
    const discountRowTotalProducts = document.querySelector('.cart-summary__row--quantity');
    const discountValueTotalProducts = discountRowTotalProducts ? discountRowTotalProducts.querySelector('.cart-summary__value') : null;
    const discountTextTotalProducts = discountRowTotalProducts ? discountRowTotalProducts.querySelector('.cart-summary__label') : null;

    if (!discountRowTotalProducts || !discountValueTotalProducts || !discountTextTotalProducts) return;

    if (productPrice > 0) {
        const formattedAmount = formatCurrency(productPrice);
        discountValueTotalProducts.textContent = `${formattedAmount} ₽`;
        discountTextTotalProducts.textContent = `Товары, ${productsCount} шт`
    }
}

function declineProductWord(count) {
    const lastDigit = count % 10;
    const lastTwoDigits = count % 100;

    if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
        return 'товаров';
    }

    if (lastDigit === 1) {
        return 'товар';
    }

    if (lastDigit >= 2 && lastDigit <= 4) {
        return 'товара';
    }

    return 'товаров';
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-top__count');
    if (cartCountElement) {
        const productWord = declineProductWord(count);
        cartCountElement.textContent = `${count} ${productWord}`;
    }
}

function updateDiscountDisplay(discountAmount) {
    const discountRow = document.querySelector('.cart-summary__row--discount');
    const discountValue = discountRow ? discountRow.querySelector('.cart-summary__value') : null;

    if (!discountRow || !discountValue) return;

    if (discountAmount > 0) {
        const formattedAmount = formatCurrency(discountAmount);
        discountValue.textContent = `-${formattedAmount} ₽`;
        discountRow.classList.remove('hidden');
    } else {
        discountRow.classList.add('hidden');
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('ru-RU').format(amount);
}