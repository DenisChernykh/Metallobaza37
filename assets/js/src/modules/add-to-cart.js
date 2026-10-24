import { removeProductToCartApi, updateProductToCartApi } from './cartv2/api';

export async function initAddToCart() {
  document.querySelectorAll('.product-info__order').forEach((item) => {
    const cartBtn = item.querySelector('.js-add-to-cart');

    cartBtn?.addEventListener('click', () => {
			const productId = item.dataset.productId;
      if (productId) {
        // Проверяем, все ли опции выбраны
        const validation = validateAllOptionsSelected();
        if (!validation.isValid) {
          alert(
            `Пожалуйста, выберите: ${validation.unselectedGroups.join(', ')}`
          );
          return;
        }

        // Получаем ID выбранных опций
        const optionIds = getSelectedOptionIds();
        const rezCheckbox = document.querySelector(
          '.product-services__checkbox'
        );
        const rezValue = rezCheckbox && rezCheckbox.checked ? 1 : 0;

        // Добавляем товар в корзину
        addProductToCart(productId, optionIds, rezValue);

      }
    });
  });

  document.querySelectorAll('.quantity').forEach((el) => {
    if (el.closest('.product-list-item')) return;
    if (el.closest('.product-info__order')) return;

    const textEl = el.querySelector('.quantity__input');
    const valueEl = el.querySelector('.quantity__value');
    const plus = el.querySelector('.quantity__btn--plus');
    const minus = el.querySelector('.quantity__btn--minus');
    const qtyControl = el;
    const cartBtn = el.querySelector('.btn--add-to-cart');
    const quantityBoxBlock = el.querySelector('.quantity-block');

    cartBtn?.addEventListener('click', () => {
      const hasOptions = qtyControl.dataset.options;
      const redirectUrl = qtyControl.dataset.href;
      if (!hasOptions || hasOptions === '1') {
        if (redirectUrl) {
          window.location.href = redirectUrl;
          return;
        } else {
          console.warn('Не указан data-href для редиректа');
        }
      }

      const productId = qtyControl.dataset.productId;
      const allProductElements = document.querySelectorAll(
        `[data-product-id="${productId}"]`
      );

      allProductElements.forEach((element) => {
        const elementCartBtn = element.querySelector('.btn--add-to-cart');
        const elementQuantityBoxBlock =
          element.querySelector('.quantity-block');
        const elementTextEl = element.querySelector('.quantity__input');

        if (elementCartBtn) elementCartBtn.classList.add('hidden');
        if (elementQuantityBoxBlock)
          elementQuantityBoxBlock.classList.remove('hidden');
        if (elementTextEl) elementTextEl.value = 1;
      });

      if (productId) {
        addProductToCart(productId, null, 0);
      }
      showAllItemsInCart();
    });

    plus?.addEventListener('click', () => {
      let current = Number(textEl.value);
      current++;
      textEl.value = current;

      const productId = qtyControl.dataset.productId;
      const allProductElements = document.querySelectorAll(
        `[data-product-id="${productId}"]`
      );
      allProductElements.forEach((element) => {
        const elementTextEl = element.querySelector('.quantity__input');
        if (elementTextEl && element !== qtyControl) {
          elementTextEl.value = current;
        }
      });

      if (qtyControl) {
        let cartId = qtyControl.dataset.cartId;
        let quantity = current;

        updateProductToCart(cartId, quantity);
      }
      showAllItemsInCart();
    });

    minus?.addEventListener('click', () => {
      let current = Number(textEl.value);
      const productId = qtyControl.dataset.productId;

      if (current > 1) {
        current--;
        textEl.value = current;

        const allProductElements = document.querySelectorAll(
          `[data-product-id="${productId}"]`
        );
        allProductElements.forEach((element) => {
          const elementTextEl = element.querySelector('.quantity__input');
          if (elementTextEl && element !== qtyControl) {
            elementTextEl.value = current;
          }
        });

        if (qtyControl) {
          let cartId = qtyControl.dataset.cartId;
          let quantity = current;

          updateProductToCart(cartId, quantity);
        }

        showAllItemsInCart();
      } else {
        const allProductElements = document.querySelectorAll(
          `[data-product-id="${productId}"]`
        );
        allProductElements.forEach((element) => {
          const elementCartBtn = element.querySelector('.btn--add-to-cart');
          const elementQuantityBoxBlock =
            element.querySelector('.quantity-block');
          const elementTextEl = element.querySelector('.quantity__input');

          if (elementCartBtn) elementCartBtn.classList.remove('hidden');
          if (elementQuantityBoxBlock)
            elementQuantityBoxBlock.classList.add('hidden');
          if (elementTextEl) elementTextEl.value = 0;
        });

        if (qtyControl) {
          let cartId = qtyControl.dataset.cartId;
          removeProductToCart(cartId);
        }
        showAllItemsInCart();
      }
    });
  });

  document.querySelectorAll('.product-list-item').forEach((item) => {
    const cartBtn = item.querySelector('.btn--add-to-cart');
    const quantityBox = item.querySelector('.quantity');
    const quantityBoxBlock = item.querySelector('.quantity-block');
    const textEl = item.querySelector('.quantity__input');
    const valueEl = item.querySelector('.quantity__value');
    const plus = item.querySelector('.quantity__btn--plus');
    const minus = item.querySelector('.quantity__btn--minus');
    const quantityText = item.querySelector('.quantity__text');

    cartBtn?.addEventListener('click', () => {
      const qtyControl = item.querySelector('[data-qty-control]');

      const hasOptions = qtyControl.dataset.options;
      const redirectUrl = qtyControl.dataset.href;
      if (!hasOptions || hasOptions === '1') {
        if (redirectUrl) {
          window.location.href = redirectUrl;
          return;
        } else {
          console.warn('Не указан data-href для редиректа');
        }
      }

      const productId = qtyControl.dataset.productId;

      const allProductElements = document.querySelectorAll(
        `[data-product-id="${productId}"]`
      );
      allProductElements.forEach((element) => {
        const elementCartBtn = element.querySelector('.btn--add-to-cart');
        const elementQuantityBoxBlock =
          element.querySelector('.quantity-block');
        const elementTextEl = element.querySelector('.quantity__input');

        if (elementCartBtn) elementCartBtn.classList.add('hidden');
        if (elementQuantityBoxBlock)
          elementQuantityBoxBlock.classList.remove('hidden');
        if (elementTextEl) elementTextEl.value = 1;
      });

      if (productId) {
        addProductToCart(productId, null, 0);
      }
      showAllItemsInCart();
    });

    plus?.addEventListener('click', () => {
      const qtyControl = item.querySelector('[data-qty-control]');

      let current = Number(textEl.value);
      if (!current) {
        current = Number(valueEl.value);
      }
      current++;
      textEl.value = current;
      if (valueEl) {
        valueEl.value = current;
      }
      if (quantityText) {
        quantityText.textContent = current;
      }

      const productId = qtyControl.dataset.productId;
      const allProductElements = document.querySelectorAll(
        `[data-product-id="${productId}"]`
      );
      allProductElements.forEach((element) => {
        const elementTextEl = element.querySelector('.quantity__input');
        if (elementTextEl && element !== qtyControl) {
          elementTextEl.value = current;
        }
      });

      if (qtyControl) {
        let cartId = qtyControl.dataset.cartId;
        let quantity = current;

        updateProductToCartApi(cartId, quantity);
      }
      showAllItemsInCart();
    });

    minus?.addEventListener('click', () => {
      const qtyControl = item.querySelector('[data-qty-control]');

      let current = Number(textEl.value);
      if (!current) {
        current = Number(valueEl.value);
      }
      const productId = qtyControl.dataset.productId;

      if (current > 1) {
        current--;
        textEl.value = current;
        if (valueEl) {
          valueEl.value = current;
        }
        if (quantityText) {
          quantityText.textContent = current;
        }

        const allProductElements = document.querySelectorAll(
          `[data-product-id="${productId}"]`
        );
        allProductElements.forEach((element) => {
          const elementTextEl = element.querySelector('.quantity__input');
          if (elementTextEl && element !== qtyControl) {
            elementTextEl.value = current;
          }
        });

        if (qtyControl) {
          let cartId = qtyControl.dataset.cartId;
          let quantity = current;

          updateProductToCartApi(cartId, quantity);
        }

        showAllItemsInCart();
      } else {
        const allProductElements = document.querySelectorAll(
          `[data-product-id="${productId}"]`
        );
        allProductElements.forEach((element) => {
          const elementCartBtn = element.querySelector('.btn--add-to-cart');
          const elementQuantityBoxBlock =
            element.querySelector('.quantity-block');
          const elementTextEl = element.querySelector('.quantity__input');

          if (elementCartBtn) elementCartBtn.classList.remove('hidden');
          if (elementQuantityBoxBlock)
            elementQuantityBoxBlock.classList.add('hidden');
          if (elementTextEl) elementTextEl.value = 0;
        });

        if (qtyControl) {
          let cartId = qtyControl.dataset.cartId;
          removeProductToCartApi(cartId);
        }

        showAllItemsInCart();
      }
    });
  });
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

// Функция для проверки, все ли опции выбраны
function validateAllOptionsSelected() {
  const variantGroups = document.querySelectorAll('[data-variant-group]');
  const unselectedGroups = [];

  variantGroups.forEach((group) => {
    const activeButton = group.querySelector('[data-variant-button].active');
    const groupTitle =
      group.previousElementSibling?.textContent?.replace(':', '') || 'Опция';

    if (!activeButton) {
      unselectedGroups.push(groupTitle);
    }
  });

  return {
    isValid: unselectedGroups.length === 0,
    unselectedGroups: unselectedGroups,
  };
}

function showAllItemsInCart() {
  // const cartIcon = document.querySelector('.cart-icon__quantity');
  // const allItems = document.querySelectorAll('.quantity__text');
  //
  // let sum = 0;
  // allItems.forEach((item) => {
  //   sum += Number(item.textContent);
  // });
  // cartIcon.textContent = sum;
  // sum += getQuickAdds();
  //
  // if (cartIcon) cartIcon.textContent = sum;
}

function addToCartWithToast(e) {
  const btn = e.currentTarget;
  if (!btn) return;

  const card = btn.closest('.product-card');
  if (!card) return;

  const imgEl = card.querySelector('.product-card__image img');
  const titleEl = card.querySelector('.product-card__title');

  const img = imgEl?.getAttribute('src') ?? '';
  const title = titleEl?.textContent?.trim() ?? '';
  incQuickAdds(1);
  showAllItemsInCart();

  renderToast(img, title);
}

function incrementCart(delta) {
  const cartIcon = document.querySelector('.cart-icon__quantity');
  if (!cartIcon) return;
  const n = parseInt(cartIcon.textContent || '0', 10) || 0;
  cartIcon.textContent = String(n + delta);
}

function renderToast(imgSrc, title) {
  document.querySelectorAll('.toast').forEach((t) => t.remove());

  const div = document.createElement('div');
  div.className = 'toast';
  div.setAttribute('role', 'status');
  div.innerHTML = `
    <div class="toast__row">
      <div class="toast__img">${
        imgSrc ? `<img src="${imgSrc}" alt="">` : ''
      }</div>
      <div class="toast__block">
        <div class="toast__title">В корзине</div>
        <div class="toast__text">${title}</div>
        <button class="toast__close" type="button" aria-label="Закрыть уведомление"></button>
      </div>
    </div>
  `;

  div.querySelector('.toast__close')?.addEventListener('click', () => {
    div.classList.remove('toast--show');
    setTimeout(() => div.remove(), 300);
  });

  document.body.appendChild(div);

  requestAnimationFrame(() => div.classList.add('toast--show'));
  setTimeout(() => {
    div.classList.remove('toast--show');
    setTimeout(() => div.remove(), 300);
  }, 2500);
}

function getQuickAdds() {
  const el = document.querySelector('.cart-icon__quantity');
  if (!el) return 0;
  return parseInt(el.dataset.quickAdds || '0', 10) || 0;
}
function incQuickAdds(delta) {
  const el = document.querySelector('.cart-icon__quantity');
  if (!el) return;
  const next = Math.max(0, getQuickAdds() + delta);
  el.dataset.quickAdds = String(next);
}

function addProductToCart(productId, optionIds = null, rez = null) {
  const requestData = {
    product_id: parseInt(productId),
    quantity: 1,
    rez: rez,
  };

  if (optionIds && optionIds.length > 0) {
    requestData.option_ids = optionIds;
  }

  // const data = await addProductToCartApi(productId, optionIds);
  if (data) {
    const elementsToUpdate = document.querySelectorAll(`
            [data-qty-control][data-product-id="${productId}"],
            .product-info__order[data-product-id="${productId}"]
          `);

    elementsToUpdate.forEach((element) => {
      element.dataset.cartId = data.data.cart_id;
    });

    updateCartCounter(data.data.count);
  }
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
  } else {
    cartCounter.classList.add('hidden');
  }
}
