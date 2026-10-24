export default class Disclosure {
  constructor(root, { plugins = [] } = {}) {
    this.root = root;
    this.trigger = root.querySelector(':scope  [data-toggle]');
    this.panel = root.querySelector(':scope  [data-panel]');
    this.plugins = plugins;
    this.isOpen = false;
    if (
      this.root.classList.contains('is-open') ||
      this.root.dataset.open === 'true'
    ) {
      this.isOpen = true;
      this.root.classList.add('is-open');
    } else {
      this.isOpen = false;
    }

    this.init();
  }

  init() {
    if (!this.panel) return;

    if (this.trigger) {
      this.trigger.addEventListener('click', (e) => {
        this.toggle(e.currentTarget);
      });
    }
    this.plugins.forEach((plugin) => plugin.init?.(this));
  }
  open(initiator = null) {
    this.initiator = initiator;
    this.plugins.forEach((plugin) => plugin.beforeOpen?.(this));
    this.root.classList.add('is-open');
    this.isOpen = true;
    this.plugins.forEach((plugin) => plugin.onOpen?.(this));
  }
  close() {
    this.plugins.forEach((plugin) => plugin.beforeClose?.(this));
    this.root.classList.remove('is-open');
    this.isOpen = false;
    this.plugins.forEach((plugin) => plugin.onClose?.(this));
  }
  toggle(initiator = null) {
    this.isOpen ? this.close() : this.open(initiator);
  }
}
