import { QuantityControl } from './QuantityControl';
import { RemoveControl } from './RemoveControl';
import { SelectControl } from './SelectControl';

export class CartPageController {
  constructor({ root, store }) {
    this.root = root;
    this.store = store;

    this.chkSelectAll = root.querySelector('[data-cart-select-all]');
    this.btnDeleteSelected = root.querySelector('[data-cart-delete-selected]');

    this.quantityControls = [];
    this.removeControls = [];
    this.selectControls = [];

    this._initItemControls();

    this._onToggleSelectAll = this._onToggleSelectAll.bind(this);
    this._onDeleteSelected = this._onDeleteSelected.bind(this);

    this.chkSelectAll?.addEventListener('change', this._onToggleSelectAll);
    this.btnDeleteSelected?.addEventListener('click', this._onDeleteSelected);

    this._unsubscribe = this.store.subscribe(() => this._renderBulkUI());
    this._renderBulkUI();
  }
  destroy() {
    this.chkSelectAll?.removeEventListener('change', this._onToggleSelectAll);
    this.btnDeleteSelected?.removeEventListener(
      'click',
      this._onDeleteSelected
    );
    this._unsubscribe?.();

    for (const qty of this.quantityControls) qty.destroy();
    for (const remove of this.removeControls) remove.destroy();
  }

  _initItemControls() {
    const itemNodes = this.root.querySelectorAll('[data-product-id]');
    for (const item of itemNodes) {
      const qtyRoot = item.querySelector('[data-qty-control]');
      if (qtyRoot) {
        this.quantityControls.push(new QuantityControl(qtyRoot, this.store));
      }
      const removeBtn = item.querySelector('[data-action="remove"]');
      if (removeBtn) {
        this.removeControls.push(new RemoveControl(removeBtn, this.store));
      }
      const selectCheckbox = item.querySelector('[data-cart-item-checkbox]');
      if (selectCheckbox) {
        this.selectControls.push(new SelectControl(selectCheckbox, this.store));
        selectCheckbox.addEventListener('change', () => this._renderBulkUI());
      }
    }
  }

  _getSelectedIds() {
    const ids = [];
    for (const selectControl of this.selectControls) {
      if (selectControl.isChecked()) {
        const id = selectControl.getId();
        if (id) {
          ids.push(id);
        }
      }
    }
    return ids;
  }
  _onToggleSelectAll() {
    const check = !!this.chkSelectAll?.checked;
    for (const selectControl of this.selectControls) {
      selectControl.setChecked(check);
    }
    this._renderBulkUI();
  }
  _onDeleteSelected(e) {
    e.preventDefault();
    const ids = this._getSelectedIds();
    if (!ids.length) return;
    for (const selectControl of this.selectControls) {
      if (ids.includes(selectControl.getId())) {
        selectControl.setChecked(false);
      }
    }
    if (typeof this.store.removeMany === 'function') {
      this.store.removeMany(ids);
    } else {
      for (const id of ids) this.store.setQty(id, 0);
    }
  }
  _renderBulkUI() {
    const anySelected = this._getSelectedIds().length > 0;
    if (this.btnDeleteSelected) this.btnDeleteSelected.disabled = !anySelected;

    const total = this.selectControls.length;
    const checked = this.selectControls.filter((selectControl) =>
      selectControl.isChecked()
    ).length;
    if (this.chkSelectAll) {
      this.chkSelectAll.indeterminate = checked > 0 && checked < total;
      this.chkSelectAll.checked = total > 0 && checked === total;
    }
  }
}
