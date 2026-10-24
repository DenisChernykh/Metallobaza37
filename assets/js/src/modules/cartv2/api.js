export async function updateProductToCartApi(cartId, quantity) {
  const res = await fetch('/wp-content/plugins/VG_Catalog/router.php', {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'cart-update',
      cart_id: cartId,
      quantity: quantity,
    }),
  });
  const data = await res.json();
  if (data.success) return data;
}

export async function removeProductToCartApi(cartId) {
  const res = await fetch('/wp-content/plugins/VG_Catalog/router.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'cart-remove',
      cart_id: cartId,
    }),
  });
 
}

export async function addProductToCartApi(productId, optionIds = null) {
  const res = await fetch('/wp-content/plugins/VG_Catalog/router.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'cart-add',
      product_id: productId,
      quantity: 1,
      option_ids: optionIds,
    }),
  });
  const data = await res.json();
  if (data.success) return data;
}
