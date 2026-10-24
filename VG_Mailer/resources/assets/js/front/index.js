document.addEventListener('DOMContentLoaded', async () => {
  await vgMailer.start();
});

/** Описания находятся в README.md */
class VGMailerConfig {
  apiPath = `${window.location.href}/`;
  submitAction = 'vg-mailer-form-submit';

  formSelector = '[data-vgform]';
  formNameAttribute = 'data-vgform-name';
  fromAttribute = 'data-vgform-from';
  agreeAttribute = 'data-vgform-agree';
  formErrorAttribute = 'data-vgform-form-error';
  captchaAttribute = 'data-vgform-captcha';

  labelAttribute = 'data-vgform-label';
  radioAttribute = 'data-vgform-radio';
  checkboxAttribute = 'data-vgform-checkbox';
  exceptionAttribute = 'data-vgform-exception';
  canBeEmpty = 'data-vgform-empty';
  disabledDivErrorAttribute = 'data-vgform-disable-error-block';

  successAttribute = 'data-vgform-ok';
  errorAttribute = 'data-vgform-error';
  alertSuccessAttribute = 'data-vgform-alert';
  alertErrorAttribute = 'data-vgform-alert-error';
  preloaderClass = 'preloader';
  loadingClass = 'loading';

  activeClass = 'active';
  inputErrorClass = 'error';
  divErrorClass = 'error-text';

  divErrorText = 'Обязательное поле';
  divErrorTelText = 'Неправильно заполнен номер';
}

class VGMailerInputModel {
  key = '';
  name = '';
  value = '';
  type = '';
}

class VGMailer {
  _config = new VGMailerConfig();
  /** @var VGMailerForm[] */
  _$forms = [];

  async start() {
    this._checkDebugParam();
    this._initForms();
    await this._initCaptcha();
    await VGMailerLog.showBackendLogs();

    VGMailerLog.log('success', 'Формы готовы к отправке!');
  }

  showInitForms() {
    /** @var VGMailerForm */
    for (const $form of this._$forms) {
      VGMailerLog.hardLog('info', $form._$form);
    }
  }

  getFormByHtml($form) {
    for (const item of this._$forms) {
      if (item.compareForm($form)) {
        return item;
      }
    }
  }

  isDebug() {
    return document.cookie.match(/vg-mailer-debug/) !== null;
  }

  dataToFormData(data) {
    const isObject = (value) => {
      return (
          value instanceof Object &&
          !Array.isArray(value) &&
          !(value instanceof File)
      );
    };
    const recursiveFormData = (formData, data, previousKey) => {
      if (data instanceof Object) {
        Object.keys(data).forEach((key) => {
          const value = data[key];
          if (isObject(value)) {
            return recursiveFormData(formData, value, key);
          }

          if (previousKey) {
            key = `${previousKey}[${key}]`;
          }

          if (Array.isArray(value)) {
            let index = -1;
            value.forEach((val) => {
              index++;
              if (isObject(val)) {
                return recursiveFormData(formData, val, `${key}[${index}]`);
              } else {
                formData.append(`${key}[${index}]`, val);
              }
            });
          } else {
            formData.append(key, value);
          }
        });
      }
      return formData;
    };
    return recursiveFormData(
        new FormData(),
        data,
        '',
    );
  }

  _checkDebugParam() {
    const url = new URL(window.location.href);
    if (url.searchParams.get('mailer-debug')) {
      url.searchParams.delete('mailer-debug');
      window.location.href = url.toString();
    }
  }

  _initForms() {
    for (const $form of document.querySelectorAll(
        `form${this._config.formSelector}`,
    )) {
      const vgMailerForm = new VGMailerForm($form);
      this._$forms.push(vgMailerForm);
    }
  }

  async _initCaptcha() {
    const cacheKey = 'vg-mailer-captcha-data';
    let json = VGMailerCache.get(cacheKey);

    if (!json) {
      const result = await fetch(this._config.apiPath, {
        method: 'POST',
        body: vgMailer.dataToFormData({
          action: 'vg-mailer-get-captcha',
        }),
      });
      json = await result.json();
      VGMailerCache.set(cacheKey, json);
    }

    if (json.success && json.data.status && json.data.data) {
      VGMailerCaptcha.init(json.data.data);

      for (const $form of this._$forms) {
        await $form.initCaptcha(json.data);
      }

      for (const captcha of json.data.data) {
        VGMailerCaptcha.initCaptchaByKey(captcha.key);
      }
    }
  }
}

class VGMailerForm {
  _eventSuccessSendName = 'vg-form-success';

  _config = new VGMailerConfig();
  _listeners = new VGMailerListeners();
  _name;

  _$form;
  _$submitButtons;
  _$agree;
  _$from;

  constructor($form) {
    this._$form = $form;
    this._$submitButtons = this._$form.querySelectorAll(
        'button[type="submit"], input[type="submit"]',
    );
    this._$agree = this._$form.querySelector(
        `input[type="checkbox"][${this._config.agreeAttribute}]`,
    );

    this._$form.addEventListener(
        'submit',
        (e) => this._listeners.formOnSubmit(e, this),
        false,
    );
    this._initAgree();
    this._initFormName();
    this._initFromInput();
    this._initCompleteListener();
  }

  compareForm($form) {
    return $form === this._$form;
  }

  async initCaptcha(json) {
    const captcha = json?.data;
    if (captcha.length === 0) {
      return;
    }

    let $captcha = this._$form.querySelector(
        `[${this._config.captchaAttribute}]`,
    );
    if (!$captcha) {
      $captcha = document.createElement('div');
      $captcha.setAttribute(this._config.captchaAttribute, '');
      this._$form.append($captcha);
    }

    const captchaKey =
        $captcha.getAttribute(this._config.captchaAttribute) === ''
            ? null
            : $captcha.getAttribute(this._config.captchaAttribute);
    const captchaData = captchaKey
        ? VGMailerCaptcha.getCaptchaByKey(captchaKey)
        : captcha[0];

    $captcha = VGMailerCaptcha.mutationCaptchaBlockByKey($captcha, captchaData);
    if (!$captcha) {
      VGMailerLog.log('warning', 'Капча не инициализирована', this._$form);
      VGMailerLog.log('info', captchaData);
    }
  }

  async prepareForm() {
    VGMailerLog.log('info', 'Обработка формы перед отправкой', this._$form);
    this.setFormError(false);

    /** @var VGMailerInputModel[]  */
    const data = [];
    /** @var VGMailerField[] */
    const fields = [];
    const $inputs = this._$form.querySelectorAll('input, select, textarea');

    if (this._$agree) {
      const vgMailerField = new VGMailerField(this._$agree, this);
      vgMailerField.setInputError(!this._$agree.checked);
    }

    for (const $input of $inputs) {
      if ($input.name === '') {
        if ($input !== this._$agree) {
          VGMailerLog.log('warning', 'У поля не заполнен [name]', $input);
        }
        continue;
      }

      const validatedField = this.validateInput($input);
      if (validatedField instanceof VGMailerField) {
        fields.push(validatedField);

        VGMailerEvents.validate({
          field: validatedField,
          $form: this._$form,
        });
      }
    }

    await VGMailerEvents.before({
      $form: this._$form,
      fields,
    });
    for (const item of fields) {
      if (item instanceof VGMailerField) {
        data.push(item.getModel());
      }
    }

    if (!this.formError()) {
      this._sendForm(vgMailer.dataToFormData({
        data,
        action: this._config.submitAction,
        name: this._name,
        from: this._$from.value,
      }), fields);
    }
  }

  validateInput($input) {
    const field = new VGMailerField($input, this);
    if (field.autoValidate() === false) {
      return false;
    }
    return field;
  }

  setFormError(bool) {
    if (bool) {
      this._$form.setAttribute(this._config.formErrorAttribute, '');
    } else {
      this._$form.removeAttribute(this._config.formErrorAttribute);
    }
  }

  formError() {
    return this._$form.getAttribute(this._config.formErrorAttribute) !== null;
  }

  disableSubmit(bool) {
    if (bool) {
      for (const $button of this._$submitButtons) {
        $button.setAttribute('disabled', '');
      }
    } else {
      for (const $button of this._$submitButtons) {
        $button.removeAttribute('disabled');
      }
    }
  }

  _initAgree() {
    if (this._$agree) {
      if (this._$submitButtons.length === 0) {
        VGMailerLog.log(
            'warning',
            'Кнопка отправки формы не найдена',
            this._$form,
        );
      } else {
        if (!this._$agree.name) {
          this._$agree.name = 'agree';
        }
        if (!this._$agree.getAttribute(this._config.labelAttribute)) {
          this._$agree.setAttribute(this._config.labelAttribute,
              'Согласие на обработку ПДН');
        }

        this._$agree.addEventListener('change', (e) =>
            this._listeners.agreeOnChange(e, this),
        );
        this._$agree.dispatchEvent(new Event('change'));
      }
    }
  }

  _initFormName() {
    const $name =
        this._$form.querySelector(`[${this._config.formNameAttribute}]`) ??
        (this._$form.getAttribute(this._config.formNameAttribute)
            ? this._$form
            : null);

    if (!$name) {
      VGMailerLog.log(
          'warning',
          `Укажите "${this._config.formNameAttribute}" для определения названия формы`,
          this._$form,
      );
    } else {
      const nameAttribute = $name.getAttribute(this._config.formNameAttribute);
      if (nameAttribute) {
        this._name = nameAttribute;
      } else {
        const nameByInnerText = $name.innerText;
        if (nameByInnerText === '') {
          VGMailerLog.log('warning', 'Не удалется получить название формы');
        }
        this._name = nameByInnerText;
      }
    }
  }

  _initFromInput() {
    this._$from = this._$form.querySelector(`[${this._config.fromAttribute}]`);
    if (!this._$from) {
      this._$from = document.createElement('input');
      this._$from.setAttribute(this._config.fromAttribute, '');
    }
    this._$from.value = window.location.href;
  }

  _initCompleteListener() {
    new Event(this._eventSuccessSendName, {bubbles: true});
  }

  _preloader(bool) {
    let $preloader = this._$form.querySelector(
        `.${this._config.preloaderClass}`,
    );

    if (bool) {
      if (!$preloader) {
        $preloader = document.createElement('div');
        $preloader.classList.add(this._config.preloaderClass);
        this._$form.append($preloader);
      }

      this._$form.classList.add(this._config.loadingClass);
    } else {
      $preloader?.remove();
      this._$form.classList.remove(this._config.loadingClass);
    }
  }

  _sendForm(formData, fields) {
    this.disableSubmit(true);
    this._preloader(true);

    fetch(this._config.apiPath, {
      method: 'POST',
      body: formData,
    }).then(async (e) => {
      const json = await e.json();

      if (typeof json.data?.debug === 'object') {
        this._debugLogs(json.data.debug);
      }

      if (json.success) {
        this._success();
      } else if (typeof json.data?.validateErrors === 'object') {
        for (const index of Object.keys(json.data.validateErrors)) {
          if (fields[index]) {
            fields[index].setInputError(true, json.data.validateErrors[index]);
          }
        }
      } else {
        throw new Error('Ошибка при отправке формы');
      }
    }).catch((e) => {
      VGMailerLog.log('error', 'При отправке формы произошла ошибка.', e);
      this._error();
    }).finally(() => {
      this._preloader(false);
      this.disableSubmit(false);
    });
  }

  _debugLogs(debugData) {
    const keys = Object.keys(debugData);

    for (const key of keys) {
      switch (key) {
        case 'data':
          VGMailerLog.log('info', 'Отправленные данные');
          console.table(debugData[key]?.data);
          break;
        case 'senderWorkers':
          VGMailerLog.log('info', 'Статусы отправлений');
          console.table(debugData[key]);
          break;
        case 'exception_phones':
          VGMailerLog.log('info', 'Заблокированные сервисы');
          if (Array.isArray(debugData[key]) && debugData[key].length > 0) {
            console.table(debugData[key]);
          } else if (typeof debugData[key] === 'string') {
            console.error(debugData[key]);
          } else {
            VGMailerLog.log('info', 'нет данных');
          }
          break;
        case 'created':
          if (typeof debugData[key] === 'boolean' && debugData[key] === true) {
            VGMailerLog.log('success', 'Запись была создана');
          } else {
            VGMailerLog.log('error', 'Запись не была создана');
            console.error(debugData[key]);
          }
      }
    }
  }

  async _error() {
    VGMailerEvents.error(this._$form);

    const $errorBlock = this._$form.querySelector(
        `[${this._config.errorAttribute}]`);
    const alertOptions = this._$form.getAttribute(
        this._config.alertErrorAttribute);

    // есть блок
    if ($errorBlock) {
      $errorBlock.classList.add(this._config.activeClass);
    }
    // выводим alert, если не отключен
    else if (alertOptions !== 'false') {
      await VGMailerAlert.alert('error', !!alertOptions ? {
        text: alertOptions,
      } : {});
    }
  }

  async _success() {
    await VGMailerEvents.success(this._$form);

    const $successBlock = this._$form.querySelector(
        `[${this._config.successAttribute}]`);
    const successAttributeValue =
        $successBlock?.getAttribute(this._config.successAttribute) || null;
    const alertOptions = this._$form.getAttribute(
        this._config.alertSuccessAttribute);

    // есть блок
    if ($successBlock) {
      if (successAttributeValue === 'replace') {
        this._$form.replaceWith($successBlock);
      }
      $successBlock?.classList.add(this._config.activeClass);
    }
    // выводим alert, если не отключен
    else if (alertOptions !== 'false') {
      await VGMailerAlert.alert('success', !!alertOptions ? {
        text: alertOptions,
      } : {});
    }

    this._$form.reset();
  }
}

class VGMailerListeners {
  formOnSubmit(e, vgMailerForm) {
    e.preventDefault();
    vgMailerForm.prepareForm();
  }

  agreeOnChange(e, vgMailerForm) {
    const $target = e.target;
    vgMailerForm.disableSubmit(!$target.checked);
  }
}

class VGMailerField {
  _config = new VGMailerConfig();
  /** @var VGMailerInputModel */
  model = new VGMailerInputModel();
  _type;
  _form;

  $input;

  constructor($input, form) {
    this.$input = $input;
    this._form = form;
    this._type = this.$input.nodeName;
    this._setModelData();
  }

  validate(rexExp, errorText = null) {
    if (!!this.$input.value.match(rexExp)) {
      this.setInputError(true, errorText ?? 'Ошибка валидации');
      this._form.setFormError(true);
    }
  }

  autoValidate() {
    const accessTypes = [
      'tel',
      'text',
      'number',
      'checkbox',
      'hidden',
      'file',
      'radio',
      'date',
      'email',
    ];
    const type = this.$input.type;
    const key = this.model.key;

    const isException =
        this.$input.getAttribute(this._config.exceptionAttribute) !== null;
    const isErrorName = this.model.name === '';
    const isNotAccess =
        this._type === 'INPUT' && accessTypes.indexOf(type) === -1;
    const isNotCheckedRadioOrCheckBox =
        ['radio', 'checkbox'].includes(type) && !this.$input.checked;

    this.setInputError(false);
    if (
        isException ||
        isErrorName ||
        isNotAccess ||
        isNotCheckedRadioOrCheckBox
    ) {
      return false;
    }

    if (type === 'checkbox') {
      return true;
    }

    const canBeEmpty =
        this.$input.getAttribute(this._config.canBeEmpty) !== null;
    const isEmptyValue = this.model.value === '';
    const isBadPhoneMask =
        (type === 'tel' || key === 'phone') &&
        this.model.value.replace(/[^\d]/g, '').length !== 11;

    if (canBeEmpty) {
      return !isEmptyValue;
    } else if (isEmptyValue) {
      this._form.setFormError(true);
      this.setInputError(true);
      return false;
    } else if (isBadPhoneMask) {
      this._form.setFormError(true);
      this.setInputError(true, this._config.divErrorTelText);
      return false;
    }

    return true;
  }

  getModel() {
    return this.model;
  }

  setInputError(bool, errorText = this._config.divErrorText) {
    const needErrorBlock =
        this.$input.getAttribute(this._config.disabledDivErrorAttribute) ===
        null;
    const $error_block =
        this.$input.parentNode.querySelector(
            `div.${this._config.divErrorClass}`,
        ) ?? null;

    if (bool && !$error_block) {
      this.$input.classList.add(this._config.inputErrorClass);
      if (needErrorBlock) {
        VGMailerLog.log(
            'warning',
            `Ошибка валидации: ${errorText}`,
            this.$input,
        );

        this.$input.insertAdjacentHTML(
            'afterend',
            `
					<div class="${this._config.divErrorClass}">${errorText}</div>`,
        );
      }
    } else if (!bool) {
      this.$input.classList.remove(this._config.inputErrorClass);
      $error_block?.remove();
    }
  }

  _setModelData() {
    const type = this.$input.type;
    let value = this.$input.value;

    switch (type) {
      case 'file':
        const files = this.$input.files;
        value = [];
        for (const index in Object.keys(files)) {
          value.push(files[index]);
        }
        break;
      case 'date':
        value = value.replace(/^(\d*)-(\d*)-(\d*)$/, '$3.$2.$1');
        break;
      case 'select-multiple':
        value = Array.from(this.$input.selectedOptions).
            map(((option) => option.value));
        break;
    }

    this.model.key = this.$input.name;
    this.model.name = this._getInputName();
    this.model.value = value;
    this.model.type = type;
  }

  _getInputName() {
    const labelParser = (labelTextData) => {
      const selectorRegExp = new RegExp(/^s(elector|):(.*)$/);

      if (!labelTextData) {
        return false;
      } else if (labelTextData.match(selectorRegExp)) {
        const selector = labelTextData.replace(selectorRegExp, '$2');
        const $el = this._form.querySelector(selector);

        if ($el) {
          return $el.innerText;
        } else {
          VGMailerLog.log('warning', `selector ${selector} не найден`);
        }
      }

      return labelTextData;
    };

    const vgformLabel = labelParser(
        this.$input.getAttribute(this._config.labelAttribute),
    );
    const type = this.$input.type;

    if (vgformLabel) {
      return vgformLabel;
    } else if (['radio', 'checkbox'].includes(type)) {
      const wrapper = this.$input.closest(
          `[${this._config[`${type}Attribute`]}]`,
      );
      const vgformLabel = labelParser(
          wrapper?.getAttribute(this._config.labelAttribute),
      );
      if (vgformLabel) {
        return vgformLabel;
      }
    }

    const $parent = this.$input.parentNode;
    if ($parent) {
      const $label = $parent.querySelector('label');
      if ($label) {
        return $label.innerText;
      }
    }

    const name = this.$input.name;
    if (name === '') {
      if (this.$input.getAttribute(this._config.agreeAttribute) === null) {
        VGMailerLog.log('warning', 'У инпута не заполнен [name]', this.$input);
      }
      return false;
    }
    return name;
  }
}

class VGMailerLog {
  static statuses = {
    success: 'green',
    warning: 'orange',
    error: 'red',
    info: 'blue',
  };

  static log(status, message, ...data) {
    if (vgMailer.isDebug()) {
      this._log(status, message, ...data);
    }
  }

  static hardLog(status, message, ...data) {
    this._log(status, message, ...data);
  }

  static _log(status, message, ...data) {
    console.log(
        '%cVGMailer:',
        `background: white; color: ${
            this.statuses[status] ?? 'black'
        }; padding: 0 5px 0 5px`,
        message,
        ...data,
    );
  }

  static showBackendLogs() {
    return new Promise((resolve) => {
      const config = new VGMailerConfig();

      fetch(config.apiPath, {
        method: 'POST',
        body: vgMailer.dataToFormData({
          action: 'vg-mailer-get-logs',
        }),
      }).then(r => r.json()).then(data => {
        for (const el of data.data) {
          VGMailerLog.hardLog(el.status, el.message);
        }
        resolve();
      });
    });
  }
}

class VGMailerCaptcha {
  static _config = new VGMailerConfig();
  static _captcha = {};

  static init(captcha) {
    for (const item of captcha) {
      this._captcha[item.key] = item;
    }
  }

  static getCaptchaByKey(key) {
    return this._captcha[key] ?? null;
  }

  static mutationCaptchaBlockByKey($block, captcha) {
    if (!captcha) {
      return false;
    }

    switch (captcha.key) {
      case 'yandex_smart_captcha':
        $block.classList.add('yandex_smart_captcha');
        $block.classList.add('smart-captcha');
        const $style = document.createElement('style');
        $style.appendChild(
            document.createTextNode(`
					.yandex_smart_captcha .${this._config.divErrorClass} {
						display: none;
					}
				`),
        );
        document.body.append($style);
        break;
      case 'cloudflare_captcha':
        $block.classList.add('cloudflare_captcha', 'cf-turnstile');
        $block.dataset.sitekey = captcha.client_key;
        break;
      default:
        VGMailerLog.log('warning', `Нет настроек для капчи ${captcha.key}`);
        return false;
    }

    return $block;
  }

  static initCaptchaByKey(key) {
    const captcha = this.getCaptchaByKey(key);
    if (!captcha) {
      return;
    }
    let $blocks = null;

    switch (key) {
      case 'yandex_smart_captcha':
        if (!window.smartCaptcha) {
          return;
        }
        $blocks = document.querySelectorAll('.yandex_smart_captcha');
        for (const $block of $blocks) {
          const $form = $block.closest(`form${this._config.formSelector}`);
          if (!$form) {
            continue;
          }

          const smartCaptchaObject = {
            sitekey: captcha.client_key,
            invisible: true,
            callback: (token) => {
              $form.dispatchEvent(new Event('submit'));
            },
          };
          if (vgMailer.isDebug()) {
            smartCaptchaObject.test = true;
          }
          const widgetId = window.smartCaptcha.render(
              $block,
              smartCaptchaObject,
          );
          $form.setAttribute('widgetId', widgetId.toString());
        }
        break;
      case 'cloudflare_captcha':
        if (!window.turnstile) {
          return;
        }
        $blocks = document.querySelectorAll('.cloudflare_captcha');
        for (const $block of $blocks) {
          const $form = $block.closest(`form${this._config.formSelector}`);
          if (!$form) {
            continue;
          }
          const vgMailerForm = vgMailer.getFormByHtml($form);
          vgMailerForm.disableSubmit(true);

          const smartCaptchaObject = {
            sitekey: captcha.client_key,
            appearance: 'interaction-only',
            theme: 'light', //Цвет капчи светлый (dark,light,auto)
            callback: function(token) {
              vgMailerForm.disableSubmit(false);
            },
          };

          const widgetId = window.turnstile.render($block, smartCaptchaObject);
          $form.setAttribute('widgetId', widgetId.toString());
        }
        break;
    }
    this._initBeforeCallbackByKey(key);
  }

  static _initBeforeCallbackByKey(key) {
    switch (key) {
      case 'yandex_smart_captcha':
        document.addEventListener('vg-mailer:before', async ({$form}) => {
          const $captcha = $form.querySelector(
              `[${this._config.captchaAttribute}]`,
          );
          const widgetId = $form.getAttribute('widgetId');

          if (!widgetId) {
            VGMailerLog.log('error', `${key}: widget not found`);
          }
          if ($captcha && $captcha.classList.contains('yandex_smart_captcha')) {
            VGMailerLog.log('info', `Каптча ${key} обработка...`);
            await window.smartCaptcha.execute(widgetId ?? 0);
          }
        });
        break;
      case 'cloudflare_captcha':
        document.addEventListener('vg-mailer:before', async ({$form}) => {
          const $captcha = $form.querySelector('.cf-turnstile');
          if ($captcha) {
            await turnstile.execute($captcha);
          }
        });
        break;
    }
  }
}

class VGMailerAlert {
  static _data;
  static _configs = new VGMailerConfig();

  static async _getData() {
    const cacheKey = 'vg-mailer-alert-data';

    if (!this._data) {
      const cache = VGMailerCache.get(cacheKey);
      if (cache) {
        this._data = cache;
      } else {
        const response = await fetch(this._configs.apiPath, {
          method: 'POST',
          body: vgMailer.dataToFormData({
            action: 'vg-mailer-get-alert',
          }),
        });
        const json = await response.json();
        this._data = json.data;

        VGMailerCache.set(cacheKey, this._data);
      }
    }
    return this._data;
  }

  static async _getScript() {
    if (typeof VGalert !== 'undefined') {
      return true;
    }

    const cacheKey = 'vg-mailer-alert-script';
    let data = VGMailerCache.get(cacheKey);

    if (!data) {
      const response = await fetch(this._configs.apiPath, {
        method: 'POST',
        body: vgMailer.dataToFormData({
          action: 'vg-mailer-get-alert-script',
        }),
      });

      const json = await response.json();
      if (!json.success) {
        return false;
      }
      data = json.data;
      VGMailerCache.set(cacheKey, data);
    }

    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = data;
      s.onload = resolve;
      s.onerror = reject;
      document.body.appendChild(s);
    });

    return true;
  }

  static async alert(type = 'success', options = {}) {
    const data = await this._getData();

    const scriptExist = await this._getScript();
    const alertData = {...(data?.[type] ?? {}), ...options};

    if (scriptExist && Object.keys(alertData).length > 0) {
      new VGalert(alertData);
      return;
    }

    switch (type) {
      case 'success':
        alert('Форма успешно отправлена');
        break;
      case 'error':
        alert('Ошибка при отправке формы');
        break;
      default:
        alert('Что-то пошло не так');
    }

    VGMailerLog.log('error', 'typeof VGalert:', typeof VGalert);
    VGMailerLog.log('error', 'data:', data);
  }
}

class VGMailerCache {

  static clear() {
    localStorage.clear();
  }

  static set(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  }

  static get(key) {
    const data = localStorage.getItem(key);
    if (data) {
      return JSON.parse(data);
    }
    return false;
  }
}

class VGMailerEvents {
  static _prefix = 'vg-mailer';

  static success($form) {
    const id = $form?.id;

    const global = new CustomEvent(`${this._prefix}:success`, {bubbles: true});
    const local = new CustomEvent(`${this._prefix}:success:${id}`,
        {bubbles: true},
    );

    global.$form = $form;
    local.$form = $form;

    $form.dispatchEvent(global);
    if (id) {
      document.dispatchEvent(local);
    }
  }

  static error($form) {
    const id = $form?.id;

    const global = new CustomEvent(`${this._prefix}:error`, {bubbles: true});
    const local = new CustomEvent(`${this._prefix}:error:${id}`,
        {bubbles: true},
    );

    global.$form = $form;
    local.$form = $form;

    $form.dispatchEvent(global);
    if (id) {
      document.dispatchEvent(local);
    }
  }

  static before({$form, fields}) {
    const id = $form?.id;

    const global = new CustomEvent(`${this._prefix}:before`, {bubbles: true});
    const local = new CustomEvent(`${this._prefix}:before:${id}`,
        {bubbles: true},
    );

    global.$form = $form;
    global.fields = fields;

    local.$form = $form;
    local.fields = fields;

    $form.dispatchEvent(global);
    if (id) {
      document.dispatchEvent(local);
    }
  }

  static validate({field, $form}) {
    const key = field.model.key;

    const global = new CustomEvent(`${this._prefix}:validate`, {bubbles: true});
    const local = new CustomEvent(`${this._prefix}:validate:${key}`,
        {bubbles: true},
    );

    global.field = field;
    global.key = key;

    local.field = field;

    $form.dispatchEvent(global);
    $form.dispatchEvent(local);
  }
}

const vgMailer = new VGMailer();
