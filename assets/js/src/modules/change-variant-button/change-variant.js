export function initChangeVariant() {
  document.querySelectorAll('[data-variant-group]').forEach(bindVariantGroup);

  const initialOptionIds = getSelectedOptionIds();
  const productId = getProductId();
  const initialRezValue = getRez();
  if (productId > 0) {
    checkCart(productId, initialOptionIds, initialRezValue);
  }
}

function checkCart(productId, optionIds, rezValue) {
  const requestData = {
    route: 'cart-check-product-with-options',
    product_id: productId,
    option_ids: optionIds,
    rez: rezValue,
  };

  fetch('/wp-content/plugins/VG_Catalog/router.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(requestData),
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        let productCount = parseInt(data.data.count.count);

        if (productCount > 0) {
          let cartId = data.data.count.cart_id;

          let productPage = document.querySelectorAll(
            `.product-info__order[data-product-id="${productId}"]`
          );

          productPage.forEach((productEl) => {
            const addBtn = productEl.querySelector('.js-add-to-cart');
            if (addBtn) addBtn.classList.add('hidden');

            const qtyBlock = productEl.querySelector('.quantity');
            if (qtyBlock) qtyBlock.classList.remove('hidden');

            const qtyInput = productEl.querySelector('.quantity__text');
            if (qtyInput) qtyInput.value = productCount;

            productEl.dataset.cartId = cartId;
          });
        } else {
          let productPage = document.querySelectorAll(
            `.product-info__order[data-product-id="${productId}"]`
          );

          productPage.forEach((productEl) => {
            const addBtn = productEl.querySelector('.js-add-to-cart');
            if (addBtn) addBtn.classList.remove('hidden');

            const qtyBlock = productEl.querySelector('.quantity');
            if (qtyBlock) qtyBlock.classList.add('hidden');

            const qtyInput = productEl.querySelector('.quantity__text');
            if (qtyInput) qtyInput.value = '1';
          });
        }
      }
    });
}

function bindVariantGroup(groupEl) {
  groupEl?.addEventListener('click', (e) => {
    const target = e.target;
    const btn =
      target && target.closest ? target.closest('[data-variant-button]') : null;
    if (!btn || !groupEl.contains(btn)) return;
    if (btn.tagName === 'A') e.preventDefault();
    setActiveVariant(groupEl, btn);
    let optionIds = getSelectedOptionIds();
		let productId = getProductId();
		let rez = getRez();
    if (productId > 0) {
      checkCart(productId, optionIds, rez);
    }
  });
}

function setActiveVariant(groupEl, btn) {
  groupEl
    .querySelectorAll('[data-variant-button].active')
    .forEach((el) => el.classList.remove('active'));

  btn.classList.add('active');
  groupEl.dispatchEvent(
    new CustomEvent('variant:change', {
      detail: {
        button: btn,
        value:
          btn.dataset.value ??
          btn.getAttribute('value') ??
          btn.textContent?.trim() ??
          null,
      },
    })
  );
}

function getSelectedOptionIds() {
  const variantGroups = document.querySelectorAll('[data-variant-group]');
  const optionIds = [];

  variantGroups.forEach((group) => {
    const activeButton = group.querySelector('[data-variant-button].active');
    if (activeButton && activeButton.dataset.optionId) {
      optionIds.push(parseInt(activeButton.dataset.optionId));
    }
  });

  return optionIds;
}

function getProductId() {
  const input = document.querySelector('input[name="page-product-id"]');
  if (input && input.value) {
    return parseInt(input.value);
  }
  return 0;
}

function getRez() {
  const rezCheckbox = document.querySelector('.product-services__checkbox');
  const rezValue = rezCheckbox && rezCheckbox.checked ? 1 : 0;
  return rezValue;
}
