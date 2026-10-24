import { getContext, getElement, store } from '@wordpress/interactivity';



store('cart-item', {
  // actions: {
  //   toggleCheck() {
  //     const ctx = getContext();
  //     console.log('toggleCheck', ctx);
  //     if (!ctx) return;
  //     ctx.checked = !ctx.checked;
  //   },
  //   async removeCartItem() {
  //     const element = getElement();
  //     const elementNode = element.ref;
  //     const root = elementNode.closest('.product-list-item__item');
  //     const id = root.querySelector('[data-cart-id]').dataset.cartId;
  //     await removeProductToCart(id);
  //     await renderCartItem();
  //   },
  // },
});
