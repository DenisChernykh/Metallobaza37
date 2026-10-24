class Modals {
  static _Modals = {};

  static addModal(Modal) {
    const modal = Modal;
    this._Modals[modal.id] = modal;
  }

  static getModal(id) {
    return this._Modals[id] ?? false;
  }

  static refreshAllButtons() {
    for (const index in this._Modals) {
      this._Modals[index].refreshButtons();
    }
  }

  static closeAll() {
    for (const index in this._Modals) {
      this._Modals[index].close();
    }
  }
}

class Modal {

  id;
  $modal;
  $buttons;
  $closeButton;

  constructor($modal) {
    this.open = this.open.bind(this);
    this.close = this.close.bind(this);

    this.$modal = $modal;
    this.id = $modal.dataset.modal ?? null;
    this.refreshButtons();
    this.$closeButton = this.$modal.querySelector('.modal-close');

    Modals.addModal(this);
  }

  refreshButtons() {
    this.$buttons = document.querySelectorAll(`[data-modal-id="${this.id}"]`);
    for (const [index, $button] of this.$buttons.entries()) {
      $button.removeEventListener('click', this.open);
      $button.addEventListener('click', (e) => {this.open(e);});
    }
  }

  open(e, clear = false) {
    Modals.closeAll();

    this.$modal.classList.add('opened');
    this.$closeButton = this.$modal.querySelector('.modal-close');

    document.addEventListener('click', (e) => {
      this.close(false, e);
    });

    if (clear === true) {
      this.clear();
    }
  }

  close(clear = false, e) {
    if (e !== undefined) {
      const $target = e.target;
      const isButton = Array.prototype.indexOf.call(this.$buttons, $target) !==
          -1;
      const isCloseButton = this.$closeButton.contains($target);
      const isModal = !$target.contains(this.$modal) && !isCloseButton;

      if (isButton || isModal) {
        return;
      }
    }

    this.$modal.classList.remove('opened');

    if (clear === true) {
      this.clear();
    }
  }

  clear() {
    const nodeName = this.$modal.nodeName;

    if (nodeName === 'FORM') {
      this.$modal.reset();
    } else {
      const $form = this.$modal.querySelector('form');
      $form?.reset();
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  (function() {
    // Привязка кнопок к модалкам
    const $modals = document.querySelectorAll('[data-modal]');
    for (const [index, $item] of $modals.entries()) {
      new Modal($item);
    }
  })();
});