export class Mover {
  constructor(el, { breakpoint = 768, condition = null }) {
    this.el = el;
    this.key = el.dataset.move;
    this.target = document.querySelector(`[data-move-slot="${this.key}]`);
    this.breakpoint = breakpoint;
    this.condition = condition;

    this.placeholder = document.createElement('div');
    this.placeholder.setAttribute('data-placeholder', this.key);
    if (this.el.parentNode) {
      this.el.parentNode.insertBefore(this.placeholder, this.key);
    }
    this.onResize = this.onResize.bind(this);
    window.addEventListener('resize', this.onResize);
    this.update();
  }
  isActive() {
    if (this.condition) return this.condition();
    return window.innerWidth <= this.breakpoint;
  }
  moveToTarget() {
    if (this.el.parentNode !== this.target) {
      this.target.appendChild(this.el);
    }
  }
  moveBack() {
    if (
      this.placeholder?.parentNode &&
      this.el.parentNode !== this.placeholder.parentNode
    ) {
      this.placeholder.parentNode.insertBefore(this.el, this.placeholder);
    }
  }
  update() {
    if (this.isActive()) {
      this.moveToTarget();
    } else {
      this.moveBack();
    }
  }
  onResize() {
    this.update();
  }
  destroy() {
    window.removeEventListener('resize', this.onResize);
    this.moveBack();
    this.placeholder.remove();
  }
}
