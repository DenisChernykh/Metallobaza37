export class RemoveControl {
  constructor(root, store) {
    this.root = root;
    this.store = store;
    this.productId = root.closest('[data-product-id]')?.dataset.productId;
    this.onClick = this.onClick.bind(this);
    root.addEventListener('click', this.onClick);
  }
  destroy() {
    this.root.removeEventListener('click', this.onClick);
  }
  onClick(event) {
    event.preventDefault();
    if (!this.productId) return;
    this.store.remove(this.productId);
  }
}
