import { CartPageController } from './CartPageController';
import { CartStore } from './CartStore';
import { QuantityControl } from './QuantityControl';
const fakeItems = [
  { id: '1', qty: 2, price: 54000, title: 'Окно 1050×1600', status: 'idle' },
  { id: '2', qty: 0, price: 32000, title: 'Окно 900×1200', status: 'idle' },
  { id: '3', qty: 0, price: 32000, title: 'Окно 900×1200', status: 'idle' },
  { id: '4', qty: 3, price: 32000, title: 'Окно 900×1200', status: 'idle' },
  { id: '5', qty: 4, price: 32000, title: 'Окно 900×1200', status: 'idle' },
  { id: '6', qty: 5, price: 32000, title: 'Окно 900×1200', status: 'idle' },
];

export const initCartControls = () => {
  // const transport = async ({ id, qty }) => {
  //   const res = await fetch('/wp-json/cart', {
  //     method: 'POST',
  //     headers: {
  //       'Content-Type': 'application/json',
  //     },
  //     credentials: 'same-origin',
  //     body: JSON.stringify({ id, qty }),
  //   });
  //   if (!res.ok) {
  //     throw new Error(res.statusText);
  //   }
  // };
  // const store = new CartStore({ transport });
  const store = new CartStore();
  for (const item of fakeItems) {
    store.setQty(item.id, item.qty, item);
  }
  const cartRoot = document.querySelector('[data-cart-root]');
  let qtyControls = [];
  let pageController = null;

  if (cartRoot) {

    pageController = new CartPageController({ root: cartRoot, store });


    const outsideControls = Array.from(
      document.querySelectorAll('[data-qty-control]')
    ).filter((el) => !cartRoot.contains(el));

    qtyControls = outsideControls.map((el) => new QuantityControl(el, store));
  } else {
   
    qtyControls = Array.from(
      document.querySelectorAll('[data-qty-control]')
    ).map((el) => new QuantityControl(el, store));
  }
  window.__cart = store;
  window.__qtyControls = qtyControls;
  window.__cartPage = pageController;
};
