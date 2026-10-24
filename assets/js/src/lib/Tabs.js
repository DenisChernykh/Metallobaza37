export default class Tabs {
  constructor(root) {
    this.root = root;
    this.triggers = [...root.querySelectorAll('[data-triggers] [data-tab]')];
    this.panels = [...root.querySelectorAll('[data-panels] [data-panel]')];
    this.init();
  }

  init() {
    if (!this.triggers.length || !this.panels.length) return;

    this.triggers.forEach((trigger, index) => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        this.select(index);
      });
    });

    this.select(0);
  }

  select(index) {
    this.triggers.forEach((el, i) => {
      el.classList.toggle('is-active', i === index);
    });

    this.panels.forEach((el, i) => {
      el.classList.toggle('is-active', i === index);
    });
  }
}
