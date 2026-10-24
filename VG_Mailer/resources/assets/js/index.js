document.addEventListener("DOMContentLoaded", async () => {
    await vgMailer.start();
});

/** Описания находятся в README.md */
class VGMailerConfig {
    formSelector = "[data-vgform]";
    apiPath = `${window.location.origin}/`;
    fromAttribute = "data-vgform-from";
    agreeAttribute = "data-vgform-agree";
    radioAttribute = "data-vgform-radio";
    checkboxAttribute = "data-vgform-checkbox";
    formNameAttribute = "data-vgform-name";
    labelAttribute = "data-vgform-label";
    successAttribute = "data-vgform-ok";
    errorAttribute = "data-vgform-error";
    activeClass = "active";
    preloaderClass = "preloader";
    disabledDivErrorAttribute = "data-vgform-disable-error-block";
    formErrorAttribute = "data-vgform-form-error";
    inputErrorClass = "error";
    divErrorClass = "error-text";
    divErrorText = "Обязательное поле";
    divErrorTelMask = "Неправильно заполнен номер";
    exceptionAttribute = "data-vgform-exception";
    canBeEmpty = "data-vgform-empty";
    captchaAttribute = "data-vgform-captcha";
    globalSuccessAlert = "isVGFormSuccess";
    globalErrorAlert = "isVGFormError";
}

class VGMailerCallbacks {
    static _acceptActions = [
        "success",
        "before",
        "error",
    ];
    static _hooks = [];

    static add(action, callback) {
        if (this._acceptActions.indexOf(action) === -1) {
            VGMailerLog.log(
                "warning",
                `Callback: такого метода "${action}" не существует`
            );
            return;
        }
        if (typeof callback !== "function") {
            VGMailerLog.log(
                "warning",
                `Callback: метод "${action}" должен быть функцией`
            );
        }

        if (this._hooks[action] === undefined) {
            this._hooks[action] = [];
        }
        this._hooks[action].push(callback);
    }

    static async run(action, $form) {
        if (this._hooks[action]) {
            for (const index in this._hooks[action]) {
                await this._hooks[action][index]($form);
            }
        }
    }
}

class VGMailerInputModel {
    key = "";
    name = "";
    value = "";
}

class VGMailer {
    _config = new VGMailerConfig();
    /** @var VGMailerForm[] */
    _$forms = [];

    async start() {
        this._checkDebugParam();
        this._initForms();
        await this._initCaptcha();

        VGMailerLog.log("success", "Формы готовы к отправке!");
    }

    showInitForms() {
        /** @var VGMailerForm */
        for (const $form of this._$forms) {
            VGMailerLog.hardLog("info", $form._$form);
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

    _checkDebugParam() {
        const url = new URL(window.location.href);
        if (url.searchParams.get('mailer-debug')) {
            url.searchParams.delete('mailer-debug');
            window.location.href = url.toString();
        }
    }

    _initForms() {
        for (const $form of document.querySelectorAll(
            `form${this._config.formSelector}`
        )) {
            const vgMailerForm = new VGMailerForm($form);
            this._$forms.push(vgMailerForm);
        }
    }

    async _initCaptcha() {
        const formData = new FormData();
        formData.append("action", "vg-mailer-get-captcha");

        const result = await fetch(this._config.apiPath, {
            method: "POST",
            body: formData,
        });
        const json = await result.json();

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
    _eventSuccessSendName = "vg-form-success";

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
            'button[type="submit"], input[type="submit"]'
        );
        this._$agree = this._$form.querySelector(
            `input[type="checkbox"][${this._config.agreeAttribute}]`
        );

        this._$form.addEventListener(
            "submit",
            (e) => this._listeners.formOnSubmit(e, this),
            false
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
            `[${this._config.captchaAttribute}]`
        );
        if (!$captcha) {
            $captcha = document.createElement("div");
            $captcha.setAttribute(this._config.captchaAttribute, "");
            this._$form.append($captcha);
        }

        const captchaKey =
            $captcha.getAttribute(this._config.captchaAttribute) === ""
                ? null
                : $captcha.getAttribute(this._config.captchaAttribute);
        const captchaData = captchaKey
            ? VGMailerCaptcha.getCaptchaByKey(captchaKey)
            : captcha[0];

        $captcha = VGMailerCaptcha.mutationCaptchaBlockByKey($captcha, captchaData);
        if (!$captcha) {
            VGMailerLog.log("warning", "Капча не инициализирована", this._$form);
            VGMailerLog.log("info", captchaData);
        }
    }

    async prepareForm() {
        VGMailerLog.log("info", "Обработка формы перед отправкой", this._$form);
        await VGMailerCallbacks.run("before", this._$form);

        this.setFormError(false);

        /** @var VGMailerInputModel[]  */
        const data = [];
        /** @var VGMailerField[] */
        const fields = [];
        const $inputs = this._$form.querySelectorAll("input, select, textarea");

        if (this._$agree) {
            const vgMailerField = new VGMailerField(this._$agree, this._$form);
            vgMailerField.setInputError(!this._$agree.checked);
        }

        for (const $input of $inputs) {
            if ($input.name === "") {
                if ($input !== this._$agree) {
                    VGMailerLog.log("warning", "У поля не заполнен [name]", $input);
                }
                continue;
            }

            const validatedField = this.validateInput($input);
            if (validatedField instanceof VGMailerField) {
                data.push(validatedField.getModel());
                fields.push(validatedField);
            }
        }

        if (!this.formError()) {
            this._sendForm(this.dataToFormData(data), fields);
        }
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
            {
                data: data,
            },
            ""
        );
    }

    validateInput($input) {
        const field = new VGMailerField($input, this._$form);
        if (field.validateInput(this) === false) {
            return false;
        }
        return field;
    }

    setFormError(bool) {
        if (bool) {
            this._$form.setAttribute(this._config.formErrorAttribute, "");
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
                $button.setAttribute("disabled", "");
            }
        } else {
            for (const $button of this._$submitButtons) {
                $button.removeAttribute("disabled");
            }
        }
    }

    _initAgree() {
        if (this._$agree) {
            if (this._$submitButtons.length === 0) {
                VGMailerLog.log(
                    "warning",
                    "Кнопка отправки формы не найдена",
                    this._$form
                );
            } else {
                this._$agree.addEventListener("change", (e) =>
                    this._listeners.agreeOnChange(e, this)
                );
                this._$agree.dispatchEvent(new Event("change"));
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
                "warning",
                `Укажите "${this._config.formNameAttribute}" для определения названия формы`,
                this._$form
            );
        } else {
            const nameAttribute = $name.getAttribute(this._config.formNameAttribute);
            if (nameAttribute) {
                this._name = nameAttribute;
            } else {
                const nameByInnerText = $name.innerText;
                if (nameByInnerText === "") {
                    VGMailerLog.log("warning", "Не удалется получить название формы");
                }
                this._name = nameByInnerText;
            }
        }
    }

    _initFromInput() {
        this._$from = this._$form.querySelector(`[${this._config.fromAttribute}]`);
        if (!this._$from) {
            this._$from = document.createElement("input");
            this._$from.setAttribute(this._config.fromAttribute, "");
        }
        this._$from.value = window.location.href;
    }

    _initCompleteListener() {
        new Event(this._eventSuccessSendName, {bubbles: true});
    }

    _preloader(bool) {
        let $preloader = this._$form.querySelector(
            `.${this._config.preloaderClass}`
        );
        if (bool && !$preloader) {
            $preloader = document.createElement("div");
            $preloader.classList.add(this._config.preloaderClass);
            this._$form.append($preloader);
        } else if (!bool) {
            $preloader?.remove();
        }
    }

    _sendForm(formData, fields) {
        this.disableSubmit(true);
        this._preloader(true);

        formData.append("action", "vg-mailer-form-submit");
        formData.append("name", this._name);
        formData.append("from", this._$from.value);

        fetch(this._config.apiPath, {
            method: "POST",
            body: formData,
        })
            .then((e) => e.json())
            .then((json) => {
                this._preloader(false);

                if (typeof json.data === "string") {
                    VGMailerLog.log('info', json.data);
                }

                if (typeof json.data?.debug === "object") {
                    const debugData = json.data.debug;

                    for (const k of Object.keys(debugData)) {
                        for (const key of Object.keys(debugData[k])) {
                            VGMailerLog.log('info', key, debugData[k][key]);
                        }
                    }
                }

                if (json.success) {
                    this._success();
                } else if (typeof json.data?.validateErrors === "object") {
                    for (const index of Object.keys(json.data.validateErrors)) {
                        if (fields[index]) {
                            fields[index].setInputError(true, json.data.validateErrors[index]);
                        }
                    }
                } else {
                    this._error();
                }

            })
            .catch((e) => {
                VGMailerLog.log("error", "При отправке формы произошла ошибка.", e);
                this._error();
            })
            .finally(() => {
                this.disableSubmit(false);
            });
    }

    async _error() {
        await VGMailerCallbacks.run("error", this._$form);
        await this.alertErrorCallback(this._$form);
    }

    async _success() {
        await VGMailerCallbacks.run("success", this._$form);
        await this.alertSuccessCallback(this._$form);

        this._$form.reset();
    }

    async alertErrorCallback() {
        const $errorBlock =
            this._$form.querySelector(`[${this._config.errorAttribute}]`) ??
            (this._$form.hasAttribute(this._config.errorAttribute) ? this._$form : null);
        const errorFunction =
            $errorBlock?.getAttribute(this._config.errorAttribute) ?? null;
        const globalErrorAlert = this._config.globalErrorAlert;

        // Есть блок и значение атрибута это функция.
        if (!!errorFunction && typeof window[errorFunction] === "function") {
            await window[errorFunction](this._$form);
        }
        // Просто есть блок.
        else if ($errorBlock) {
            $errorBlock.classList.add(this._config.activeClass);
        }
        // Если найдена глобальная функция.
        else if (!!globalErrorAlert && typeof window[globalErrorAlert] === "function") {
            await window[globalErrorAlert](this._$form);
        }
        // Если ничего нет, то alert();
        else {
            alert("Что-то пошло не так при отправке формы");
        }
    }

    async alertSuccessCallback() {
        const $successBlock =
            this._$form.querySelector(`[${this._config.successAttribute}]`) ??
            (this._$form.hasAttribute(this._config.successAttribute) ? this._$form : null);
        const successFunction =
            $successBlock?.getAttribute(this._config.successAttribute) ?? null;
        const globalSuccessAlert = this._config.globalSuccessAlert;

        if (successFunction === 'replace' && $successBlock === this._$form) {
            VGMailerLog.log(
                'warning',
                `[${this._config.successAttribute}="replace"] не работает, когда находится в <form>`
            );
        }

        // Если блок есть и значение атрибута это функция.
        if (!!successFunction && typeof window[successFunction] === "function") {
            await window[successFunction](this._$form);
        }
        // Если блок есть и значение на замену формы на блок.
        else if (successFunction === "replace" && $successBlock !== this._$form) {
            this._$form.replaceWith($successBlock);
        }
        // Если просто блок без значения.
        else if ($successBlock) {
            $successBlock.classList.add(this._config.activeClass);
        }
        // Если найдена глобальная функция.
        else if (!!globalSuccessAlert && typeof window[globalSuccessAlert] === "function") {
            await window[globalSuccessAlert](this._$form);
        }
        // Если ничего нет, то alert();
        else {
            alert("Форма успешно отправлена");
        }
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
    _model = new VGMailerInputModel();
    _type;
    _$form;

    _$input;

    constructor($input, $form) {
        this._$input = $input;
        this._$form = $form;
        this._type = this._$input.nodeName;
        this._setModelData();
    }

    validateInput(vgMailerForm) {
        const accessTypes = [
            "tel",
            "text",
            "number",
            "checkbox",
            "hidden",
            "file",
            "radio",
            "date",
            "email",
        ];
        const type = this._$input.type;

        const isException =
            this._$input.getAttribute(this._config.exceptionAttribute) !== null;
        const isErrorName = this._model.name === "";
        const isNotAccess =
            this._type === "INPUT" && accessTypes.indexOf(type) === -1;
        const isNotCheckedRadioOrCheckBox =
            ["radio", "checkbox"].includes(type) && !this._$input.checked;

        this.setInputError(false);
        if (
            isException ||
            isErrorName ||
            isNotAccess ||
            isNotCheckedRadioOrCheckBox
        ) {
            return false;
        }

        if (type === "checkbox") {
            return true;
        }

        const canBeEmpty =
            this._$input.getAttribute(this._config.canBeEmpty) !== null;
        const isEmptyValue = this._model.value === "";
        const isBadPhoneMask =
            type === "tel" && this._model.value.replace(/[^\d]/g, "").length !== 11;

        if (canBeEmpty) {
            return !isEmptyValue;
        } else if (isEmptyValue) {
            vgMailerForm.setFormError(true);
            this.setInputError(true);
            return false;
        } else if (isBadPhoneMask) {
            vgMailerForm.setFormError(true);
            this.setInputError(true, this._config.divErrorTelMask);
            return false;
        }

        return true;
    }

    getModel() {
        return this._model;
    }

    setInputError(bool, errorText = null) {
        const needErrorBlock =
            this._$input.getAttribute(this._config.disabledDivErrorAttribute) ===
            null;
        const $error_block =
            this._$input.parentNode.querySelector(
                `div.${this._config.divErrorClass}`
            ) ?? null;

        if (bool && !$error_block) {
            this._$input.classList.add(this._config.inputErrorClass);
            if (needErrorBlock) {
                VGMailerLog.log(
                    "warning",
                    `Ошибка валидации: ${this._config.divErrorText}`,
                    this._$input
                );
                this._$input.insertAdjacentHTML(
                    "afterend",
                    `
					<div class="${this._config.divErrorClass}">
						${errorText ?? this._config.divErrorText}
					</div>`
                );
            }
        } else if (!bool) {
            this._$input.classList.remove(this._config.inputErrorClass);
            if ($error_block) {
                $error_block.remove();
            }
        }
    }

    _setModelData() {
        const type = this._$input.type;
        let value = this._$input.value;

        switch (type) {
            case "file":
                const files = this._$input.files;
                value = [];
                for (const index in Object.keys(files)) {
                    value.push(files[index]);
                }
                break;
            case "date":
                value = value.replace(/^(\d*)-(\d*)-(\d*)$/, "$3.$2.$1");
                break;
        }

        this._model.key = this._$input.name;
        this._model.name = this._getInputName();
        this._model.value = value;
    }

    _getInputName() {
        const labelParser = (labelTextData) => {
            const selectorRegExp = new RegExp(/^s(elector|):(.*)$/);

            if (!labelTextData) {
                return false;
            } else if (labelTextData.match(selectorRegExp)) {
                const selector = labelTextData.replace(selectorRegExp, "$2");
                const $el = this._$form.querySelector(selector);

                if ($el) {
                    return $el.innerText;
                } else {
                    VGMailerLog.log("warning", `selector ${selector} не найден`);
                }
            }

            return labelTextData;
        };

        const vgformLabel = labelParser(
            this._$input.getAttribute(this._config.labelAttribute)
        );
        const type = this._$input.type;

        if (vgformLabel) {
            return vgformLabel;
        } else if (["radio", "checkbox"].includes(type)) {
            const wrapper = this._$input.closest(
                `[${this._config[`${type}Attribute`]}]`
            );
            const vgformLabel = labelParser(
                wrapper?.getAttribute(this._config.labelAttribute)
            );
            if (vgformLabel) {
                return vgformLabel;
            }
        }

        const $parent = this._$input.parentNode;
        if ($parent) {
            const $label = $parent.querySelector("label");
            if ($label) {
                return $label.innerText;
            }
        }

        const name = this._$input.name;
        if (name === "") {
            if (this._$input.getAttribute(this._config.agreeAttribute) === null) {
                VGMailerLog.log("warning", "У инпута не заполнен [name]", this._$input);
            }
            return false;
        }
        return name;
    }
}

class VGMailerLog {
    static statuses = {
        success: "green",
        warning: "orange",
        error: "red",
        info: "blue",
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
            "%cVGMailer:",
            `background: white; color: ${
                this.statuses[status] ?? "black"
            }; padding: 0 5px 0 5px`,
            message,
            ...data
        );
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
            case "yandex_smart_captcha":
                $block.classList.add("yandex_smart_captcha");
                $block.classList.add("smart-captcha");
                const $style = document.createElement("style");
                $style.appendChild(
                    document.createTextNode(`
					.yandex_smart_captcha .${this._config.divErrorClass} {
						display: none;
					}
				`)
                );
                document.body.append($style);
                break;
            case "cloudflare_captcha":
                $block.classList.add("cloudflare_captcha", "cf-turnstile");
                $block.dataset.sitekey = captcha.client_key;
                break;
            default:
                VGMailerLog.log("warning", `Нет настроек для капчи ${captcha.key}`);
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
            case "yandex_smart_captcha":
                if (!window.smartCaptcha) {
                    return;
                }
                $blocks = document.querySelectorAll(".yandex_smart_captcha");
                for (const $block of $blocks) {
                    const $form = $block.closest(`form${this._config.formSelector}`);
                    if (!$form) {
                        continue;
                    }

                    const smartCaptchaObject = {
                        sitekey: captcha.client_key,
                        invisible: true,
                        callback: (token) => {
                            $form.dispatchEvent(new Event("submit"));
                        },
                    };
                    if (vgMailer.isDebug()) {
                        smartCaptchaObject.test = true;
                    }
                    const widgetId = window.smartCaptcha.render(
                        $block,
                        smartCaptchaObject
                    );
                    $form.setAttribute("widgetId", widgetId.toString());
                }
                break;
            case "cloudflare_captcha":
                if (!window.turnstile) {
                    return;
                }
                $blocks = document.querySelectorAll(".cloudflare_captcha");
                for (const $block of $blocks) {
                    const $form = $block.closest(`form${this._config.formSelector}`);
                    if (!$form) {
                        continue;
                    }
                    const vgMailerForm = vgMailer.getFormByHtml($form);
                    vgMailerForm.disableSubmit(true);

                    const smartCaptchaObject = {
                        sitekey: captcha.client_key,
                        appearance: "interaction-only",
                        theme: "light", //Цвет капчи светлый (dark,light,auto)
                        callback: function (token) {
                            vgMailerForm.disableSubmit(false);
                        },
                    };

                    const widgetId = window.turnstile.render($block, smartCaptchaObject);
                    $form.setAttribute("widgetId", widgetId.toString());
                }
                break;
        }
        this._initBeforeCallbackByKey(key);
    }

    static _initBeforeCallbackByKey(key) {
        switch (key) {
            case "yandex_smart_captcha":
                VGMailerCallbacks.add("before", async ($form) => {
                    const $captcha = $form.querySelector(
                        `[${this._config.captchaAttribute}]`
                    );
                    const widgetId = $form.getAttribute("widgetId");

                    if (!widgetId) {
                        VGMailerLog.log("error", `${key}: widget not found`);
                    }
                    if ($captcha && $captcha.classList.contains("yandex_smart_captcha")) {
                        VGMailerLog.log("info", `Каптча ${key} обработка...`);
                        await window.smartCaptcha.execute(widgetId ?? 0);
                    }
                });
                break;
            case "cloudflare_captcha":
                VGMailerCallbacks.add("before", async ($form) => {
                    const $captcha = $form.querySelector(".cf-turnstile");
                    if ($captcha) {
                        await turnstile.execute($captcha);
                    }
                });
                break;
        }
    }
}

const vgMailer = new VGMailer();
