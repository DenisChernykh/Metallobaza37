
const API_URL = '/wp-content/plugins/VG_Catalog/router.php';

async function apiGetList() {
  const res = await fetch(`${API_URL}?route=cart-get-list`, {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' },
  });
  if (!res.ok) throw new Error('Не удалось получить корзину');
  const json = await res.json();
  return json?.data;
}

async function apiUpdateItem(cartId, quantity) {
  const res = await fetch(API_URL, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'cart-update',
      cart_id: cartId,
      quantity,
    }),
  });
  if (!res.ok) throw new Error('Не удалось обновить позицию');
  return res.json();
}

async function apiRemoveItem(cartId) {
  const res = await fetch(API_URL, {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'cart-remove',
      cart_id: cartId,
    }),
  });
  if (!res.ok) throw new Error('Не удалось удалить позицию');
  return res.json();
}

const fmtRub = (n) =>
  new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 }).format(
    Number(n || 0)
  );

function getUnit(unitObj) {
  if (!unitObj) return 'м.п';
  if (typeof unitObj === 'string') return unitObj || 'м.п';
  if (typeof unitObj === 'object' && unitObj.name) return unitObj.name;
  return 'м.п';
}

function priceBlock(product) {
  const price = Number(product?.price || 0);
  const sale = Number(product?.sale_price || 0);
  const hasSale = sale > 0 && sale < price;
  const unit = getUnit(product?.unit);

  const current = hasSale ? sale : price;

  return `
    <div class="product-list-item__price">
      <div class="current">${fmtRub(current)} &#8381; <span>${unit}</span></div>
      ${
        hasSale
          ? `<div class="old">${fmtRub(price)} &#8381; / ${unit}</div>`
          : ''
      }
    </div>
  `;
}

function labelsBlock(product, hasSale) {
  const hit =
    product?.is_hit === true ||
    product?.is_hit === 1 ||
    product?.is_hit === '1';

  return `
    <div class="product-list-item__labels">
      ${hasSale ? `<div class="label">Акция</div>` : ''}
      ${hit ? `<div class="label label--black">Хит</div>` : ''}
    </div>
  `;
}

function buildItemHTML(item) {
  const product = item?.product ?? {};
  const title = product?.title ?? '';
  const price = Number(product?.price || 0);
  const sale = Number(product?.sale_price || 0);
  const hasSale = sale > 0 && sale < price;
  const unit = getUnit(product?.unit);
  const quantity = Number(item?.quantity || 1);

  return `
  <div data-wp-interactive="cart-item"
       data-wp-context='{"checked": false}'
       class="product-list-item cart-list-item">

    <div class="cart-list-item__checkbox checkbox">
      <label class="checkbox__label">
        <input type="checkbox"
               class="checkbox__input"
               data-role="check-toggle"
               data-wp-on--change="actions.toggleCheck"
               data-wp-bind--checked="context.checked">
        <span class="checkbox__box"></span>
        <span class="checkbox__text"></span>
      </label>
    </div>

    <div class="product-list-item__item product-list-item__item--name">
      <div class="product-list-item__name">${title}</div>
      ${labelsBlock(product, hasSale)}
    </div>

    <div class="product-list-item__item product-list-item__item--price">
      ${priceBlock(product)}
    </div>

    <div class="product-list-item__item product-list-item__item--icons">
      <div  data-cart-id="${item?.id ?? item?.cart_id ?? ''}"
       data-product-id="${
         item?.product_id ?? product?.id ?? ''
       }" class="product-list-item__quantity quantity" data-qty-control>
        <button class="quantity__btn quantity__btn--minus" data-action="minus">-</button>
        <input type="hidden" class="quantity__value" value="${quantity}" />
        <span class="quantity__input quantity__text" data-role="qty-text">${quantity}</span>
        <button class="quantity__btn quantity__btn--plus" data-action="plus">+</button>
      </div>

      <div class="cart-list-item__icons">
        <button class="favorite-icon favorite-icon--product" type="button" aria-label="В избранное">
          <svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.96588 6.46492C4.90352 5.52756 6.17506 5.00098 7.50088 5.00098C8.8267 5.00098 10.0982 5.52756 11.0359 6.46492L12.5009 7.92867L13.9659 6.46492C14.4271 5.98737 14.9788 5.60646 15.5889 5.34441C16.1989 5.08237 16.855 4.94443 17.5189 4.93867C18.1828 4.9329 18.8412 5.0594 19.4557 5.31081C20.0701 5.56221 20.6284 5.93348 21.0979 6.40294C21.5673 6.87241 21.9386 7.43067 22.19 8.04515C22.4414 8.65963 22.5679 9.31803 22.5621 9.98192C22.5564 10.6458 22.4184 11.3019 22.1564 11.9119C21.8943 12.522 21.5134 13.0737 21.0359 13.5349L12.5009 22.0712L3.96588 13.5349C3.02853 12.5973 2.50195 11.3257 2.50195 9.99992C2.50195 8.6741 3.02853 7.40256 3.96588 6.46492Z" fill="white" stroke="#100F15" stroke-width="2" stroke-linejoin="round"/></svg>
        </button>

        <button class="button trash-icon" type="button" data-action="remove" aria-label="Удалить" data-wp-on--click="actions.removeCartItem">
          <svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.0625 6.875H20.9375M19.0625 6.875V20C19.0625 20.9375 18.125 21.875 17.1875 21.875H7.8125C6.875 21.875 5.9375 20.9375 5.9375 20V6.875M8.75 6.875V5C8.75 4.0625 9.6875 3.125 10.625 3.125H14.375C15.3125 3.125 16.25 4.0625 16.25 5V6.875" stroke="#100F15" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
  </div>`;
}

export async function renderCartItem() {
  const root = document.querySelector('[data-cart-row]');
  if (!root) return;

  try {
    const data = await apiGetList();
    const cart = Array.isArray(data?.cart)
      ? data.cart
      : Array.isArray(data)
      ? data
      : data?.items || [];

    root.innerHTML = '';
    const frag = document.createDocumentFragment();

    cart.forEach((item) => {
      const wrapper = document.createElement('div');
      wrapper.innerHTML = buildItemHTML(item).trim();
			frag.appendChild(wrapper.firstElementChild);

    });

    root.appendChild(frag);
  } catch (e) {
    console.error(e);
  }
}
