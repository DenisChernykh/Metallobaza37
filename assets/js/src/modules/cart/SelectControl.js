export class SelectControl {
  constructor(checkboxEl) {
    this.checkbox = checkboxEl;
    this.productId = checkboxEl.closest('[data-product-id]')?.dataset.productId;
  }

  isChecked() {
    return !!this.checkbox.checked;
  }

  setChecked(value) {
    this.checkbox.checked = !!value;
  }
  getId() {
    return this.productId;
  }
}
