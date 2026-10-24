export class CopyToClipboard {
  constructor({ selector = ' [data-copy]' } = {}) {
    this.selector = selector;
    this.onClick = this.onClick.bind(this);
    document.addEventListener('click', this.onClick);
  }
  onClick(e) {
    const btn = e.target.closest(this.selector);
    if (!btn) return;
    e.preventDefault();

    let text = btn.dataset.copyText;
    const targetSel = btn.dataset.copyTarget;
    if (!text && targetSel) {
      const el = document.querySelector(targetSel);
      if (el) text = 'value' in el ? el.value : (el.textContent || '').trim();
    }
    if (!text && btn.previousElementSibling) {
      const el = btn.previousElementSibling;
      text = 'value' in el ? el.value : (el.textContent || '').trim();
    }
    if (!text) text = (btn.textContent || '').trim();
    if (!text) return;
    navigator.clipboard.writeText(text);
  }
  destroy() {
    document.removeEventListener('click', this.onClick);
  }
}
