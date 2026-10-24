export function ShowErrorsPlugin({ selector = '.form-error', render, skip } = {}) {
  function getErrorEl(field) {
    let el = field.nextElementSibling;
    if (!el || !el.matches(selector)) {
      el = document.createElement('div');
      el.className = selector.replace(/^\./, '');
      field.insertAdjacentElement('afterend', el);
    }
    return el;
  }
  return {
    onFieldInvalid(_instance, field, message) {
      if (typeof skip === 'function' && skip(field)) {
        return;
      }
      if (!message) {
        return;
      }
      const el = getErrorEl(field);
      el.textContent = '';
      if (typeof render === 'function') {
        render(el, message, field);
      } else {
        el.textContent = message;
      }
    },
    onFieldValid(_instance, field) {
      if (typeof skip === 'function' && skip(field)) {
        return;
      }
      const el = field.nextElementSibling;
      if (el && el.matches(selector)) {
        el.remove();
      }
    },
  };
}
