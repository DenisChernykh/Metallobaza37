export class FormValidator {
  constructor(
    form,
    { plugins = [], rules = {}, defaultError = 'Поле заполнено неверно' } = {}
  ) {
    this.form = form;
    this.plugins = plugins;
    this.rules = rules;
    this.errors = new Map();
    this.defaultError = defaultError;
    this.init();
  }
  init() {
    if (!this.form) return;
    this.form.addEventListener('submit', (e) => {
      this.plugins.forEach((plugin) => plugin.onValidateStart?.(this));
      const ok = this.validate();
      this.plugins.forEach((plugin) => plugin.onValidateEnd?.(this, ok));
      if (!ok) {
        e.preventDefault();
        this.plugins.forEach((plugin) => plugin.onSubmitFail?.(this));
      } else {
        this.plugins.forEach((plugin) => plugin.onSubmitSuccess?.(this));
      }
    });
    this.fields().forEach((field) => {
      const handler = () => this.validateField(field);
      field.addEventListener('input', handler);
      field.addEventListener('change', handler);
      field.addEventListener('blur', handler);
    });
    this.plugins.forEach((plugin) => plugin.init?.(this));
  }
  fields() {
    return Array.from(
      this.form.querySelectorAll('input[name], textarea[name], select[name]')
    );
  }
  getValue(field) {
    if (field.type === 'checkbox') {
      return field.checked;
    }
    if (field.type === 'radio') {
      const checked = this.form.querySelector(
        `input[name="${CSS.escape(field.name)}"]:checked`
      );
      return checked ? checked.value : '';
    }
    return (field.value || '').trim();
  }
  validate() {
    this.errors.clear();
    let valid = true;
    this.fields().forEach((field) => {
      const ok = this.validateField(field);
      if (!ok) valid = false;
    });
    this.plugins.forEach((plugin) => plugin.onValidate?.(this));
    return valid;
  }
  validateField(field) {
    const name = field.name;
    const rule = this.rules[name];

    let pluginResult;
    for (const plugin of this.plugins) {
      if (typeof plugin.validateField === 'function') {
        const res = plugin.validateField(this, field);
        if (res !== undefined) {
          pluginResult = res;
          break;
        }
      }
    }
    const value = this.getValue(field);
    let error = null;
    if (pluginResult === false) {
      error = this.defaultError;
    } else if (typeof pluginResult === 'string') {
      error = pluginResult;
    } else {
      if (typeof rule === 'function') {
        const ruleResult = rule(value, field, this.form);
        error =
          typeof ruleResult === 'string' && ruleResult ? ruleResult : null;
      }
    }
    if (error) {
      this.errors.set(name, error);
      field.setAttribute('aria-invalid', 'true');
      this.plugins.forEach((plugin) =>
        plugin.onFieldInvalid?.(this, field, error)
      );
      return false;
    } else {
      this.errors.delete(name);
      field.removeAttribute('aria-invalid');
      this.plugins.forEach((plugin) => plugin.onFieldValid?.(this, field));
      return true;
    }
  }
}
