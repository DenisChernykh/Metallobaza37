import { toInt } from '../../utils/utils';

export class QuantityControl {
  constructor(root, store) {
    this.root = root;
    this.store = store;

    const card = this.root.closest('[data-product-id]');
    this.productId = card?.dataset.productId;
    this.stockHint = toInt(card?.dataset.stock, Infinity);
    this.price = toInt(card?.dataset.price, 0);

    this.btnAdd = this.root.querySelector('[data-action="add"]');
    this.wrapQty = this.root.querySelector('[data-qty-wrap]');
    this.btnDec = this.root.querySelector('[data-action="decrease"]');
    this.btnInc = this.root.querySelector('[data-action="increase"]');
    this.input = this.root.querySelector('[data-qty-input]');

    this._onClick = this._onClick.bind(this);
    this._onInput = this._onInput.bind(this);
    this._onBlur = this._onBlur.bind(this);

    this.root.addEventListener('click', this._onClick);
    if (this.input) {
      this.input.addEventListener('input', this._onInput);
      this.input.addEventListener('blur', this._onBlur);
    }
    this._unsubscribe = this.store.subscribe(({ changedId }) => {
      if (
        !this.productId ||
        (changedId && changedId !== String(this.productId))
      )
        return;
      this.render(true);
    });
    this.render(true);
  }

  destroy() {
    this.root.removeEventListener('click', this._onClick);
    if (this.input) {
      this.input.removeEventListener('input', this._onInput);
      this.input.removeEventListener('blur', this._onBlur);
    }
    this._unsubscribe?.();
  }
  _onClick(e) {
    const action = e.target.closest?.('[data-action]')?.dataset.action;
    if (!action || !this.productId) return;
    if (action === 'add') {
      this.store.setQty(this.productId, 1, {
        stock: this.stockHint,
        price: this.price,
      });
    } else if (action === 'increase') {
      const next = this.store.getQty(this.productId) + 1;
      this.store.setQty(this.productId, next, {
        stock: this.stockHint,
      });
    } else if (action === 'decrease') {
      const next = this.store.getQty(this.productId) - 1;
      this.store.setQty(this.productId, next, {
        stock: this.stockHint,
      });
    }
  }
  _onInput(event) {
    const raw = event.target.value;
    const parsed = toInt(raw, 0);
    this.store.setQty(this.productId, parsed, {
      stock: this.stockHint,
    });
  }
  _onBlur(event) {
    if (!this.productId) return;
    const parsed = toInt(event.target.value, 0);
    this.store.setQty(this.productId, parsed, { stock: this.stockHint });
  }

  render(initial = false) {
    if (!this.productId) return;

    const item = this.store.getItem(this.productId) || {
      qty: 0,
      status: 'idle',
    };
    const { qty, status } = item;

    this.root.dataset.qty = String(qty);
    if (this.input && document.activeElement !== this.input) {
      this.input.value = String(qty);
    }
    if (this.btnAdd) {
      this.btnAdd.style.display = qty === 0 ? '' : 'none';
    }
    if (this.wrapQty) this.wrapQty.style.display = qty === 0 ? 'none' : '';

    const min = toInt(this.input?.min, 0);
    const max = Number.isFinite(this.stockHint)
      ? this.stockHint
      : toInt(this.input?.max, Infinity);

    if (this.btnDec) this.btnDec.disabled = qty <= min || status === 'pending';
    if (this.btnInc) this.btnInc.disabled = qty >= max || status === 'pending';
    if (this.input) {
      this.input.disabled = status === 'pending';
      this.input.setAttribute('aria-valuenow', String(qty));
    }

    this.root.toggleAttribute('data-status-pending', status === 'pending');
    this.root.toggleAttribute('data-status-error', status === 'error');
    if (initial && qty > 0) {
      if (this.btnAdd) this.btnAdd.style.display = 'none';
      if (this.wrapQty) this.wrapQty.style.display = '';
    }
  }
}
