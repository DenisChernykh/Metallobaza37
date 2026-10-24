export default class OverlayManager {
  constructor({ selector = '.site-overlay' } = {}) {
    this.overlay = document.querySelector(selector);
  }

  open() {
    this.overlay.classList.add('is-visible');
  }
  close() {
    this.overlay?.classList.remove('is-visible');
  }
}
