import { FormValidator } from './FormValidator';

import { FocusFirstInvalidPlugin } from './plugins/FocusFirtstInvalidPlugin';
import { HighlightPlugin } from './plugins/HighLightPlugin';
import { ShowErrorsPlugin } from './plugins/ShowErrorsPlugin';

export function initForm(form) {
  const plugins = [
    HighlightPlugin(),

    ShowErrorsPlugin({
      selector: '.form-error',
      skip: (field) => field.name === 'agree',
    }),
    FocusFirstInvalidPlugin(),
  ];
  const rules = {
    name: (v) => (v ? null : 'Введите имя'),
    phone: (v) => {
      const digits = (v.match(/\d/g) || []).length;
      return digits >= 11 ? null : 'Введите корректный телефон';
    },
    agree: (_v, field) => (field.checked ? null : ' '),
    email: (value) => {
      if (!value) return 'Введите e-mail';
      return /\S+@\S+\.\S+/.test(value) ? null : 'Неверный e-mail';
    },
  };
  const instance = new FormValidator(form, { plugins, rules });
  form._validator = instance;
  return instance;
}
