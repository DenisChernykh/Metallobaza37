export async function removeItemFromCart(cartId) {
  console.log('removeItemFromCart', cartId);
  const root = document.querySelector(`[data-cart-id="${cartId}"]`);
  const removeBtn = root.querySelector('[data-action="remove"]');
  removeBtn.addEventListener('click', () => {
    console.log('remove');
  });
}
