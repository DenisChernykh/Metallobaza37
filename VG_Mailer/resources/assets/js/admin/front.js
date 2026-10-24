// password icon
document.addEventListener('DOMContentLoaded', () => {
  const inputIconContainers = document.querySelectorAll('.input-icon');

  for (const container of inputIconContainers) {
    const icon = container.querySelector('.icon-eye-password');
    const passwordInput = container.querySelector('input');

    if (!icon || typeof icon === 'undefined') continue; // Изменено с return на continue

    icon.addEventListener('click', () => {
      icon.classList.toggle('active');
      passwordInput.type = icon.classList.contains('active')
          ? 'text'
          : 'password';
    });
  }
});

// view changer
document.addEventListener('DOMContentLoaded', () => {
  const viewChanger = document.querySelector('.view-changer');
  const table = document.querySelector('.table');

  if (!viewChanger || !table) return;

  const buttons = viewChanger.querySelectorAll('.view-changer__button');
  const line = viewChanger.querySelector('.view-changer__bg-button');

  // Устанавливаем активную кнопку и класс таблицы из localStorage
  const savedView = localStorage.getItem('viewChanger');
  if (savedView) {
    buttons.forEach((button) => {
      if (button.dataset.view === savedView) {
        button.classList.add('active');
        updateTableClass(savedView);
      } else {
        button.classList.remove('active');
      }
    });
  } else {
    const defaultButton = buttons[0];
    defaultButton.classList.add('active');
    updateTableClass(defaultButton.dataset.view);
  }

  tabUnderline(buttons, line);

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      buttons.forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');
      tabUnderline(buttons, line);

      const view = button.dataset.view;
      updateTableClass(view);
      localStorage.setItem('viewChanger', view);
    });
  });

  function updateTableClass(view) {
    if (view === 'table') {
      table.classList.remove('grid');
    } else if (view === 'grid') {
      table.classList.add('grid');
    }
  }
});

function tabUnderline(
    buttons = '.view-changer .view-changer__button',
    line = '.view-changer .view-changer__bg-button',
) {
  const tabLinks =
      typeof buttons === 'string'
          ? document.querySelectorAll(buttons)
          : buttons;
  const tabLine =
      typeof line === 'string' ? document.querySelector(line) : line;
  const activeLink = [...tabLinks].find((link) =>
      link.classList.contains('active'),
  );

  if (activeLink) {
    const parent = activeLink.offsetParent;
    const parentPaddingLeftRem = parent
        ? parseFloat(getComputedStyle(parent).paddingLeft) / getRem()
        : 0;
    const parentPaddingTopRem = parent
        ? parseFloat(getComputedStyle(parent).paddingTop) / getRem()
        : 0;

    const offsetLeftRem =
        (activeLink.offsetLeft - parentPaddingLeftRem * getRem()) /
        getRem();
    const offsetWidthRem = activeLink.offsetWidth / getRem();
    const offsetTopRem =
        (activeLink.offsetTop - parentPaddingTopRem * getRem()) / getRem();

    tabLine.style.transform = `translate(${offsetLeftRem}rem, ${offsetTopRem}rem)`;
    tabLine.style.width = `${offsetWidthRem}rem`;
  }
}

function getRem() {
  return parseFloat(getComputedStyle(document.documentElement).fontSize);
}

function _fetch(action, data, callbacks = {}) {
  const formData = dataToFormData(data);
  formData.append('action', action);

  fetch(
      window.location.href,
      {
        method: 'POST',
        body: formData,
      },
  ).then(async (response) => {
    const json = await response.json();
    if (!response.ok) {
      const data = typeof json?.data === 'string' ?
          json.data :
          'Неизвестная ошибка сервера';
      throw new Error(data);
    }
    callbacks?.onSuccess && callbacks.onSuccess(data);
  }).catch((error) => {
    new VGalert({
      theme: 'light',
      title: 'Ошибка сервера',
      text: error.message,
      classes: ['error'],
      closeButton: true,
    });
    callbacks?.onError && callbacks.onError(error.message);
  });
}

function dataToFormData(data) {
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

        if (previousKey) {
          key = `${previousKey}[${key}]`;
        }

        if (isObject(value)) {
          return recursiveFormData(formData, value, key);
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
  return recursiveFormData(new FormData(), data, '');
}

function getPhoneMask() {
  return {
    mask: '+{7} (000) 000-00-00',
    definitions: {
      '0': /[0-9*]/, // Переопределяем стандартный символ '0'
    },
    prepare: (appended, masked) => {
      if (appended === '8' && masked.value === '') {
        return '7';
      }
      return appended;
    },
    lazy: false,
    overwrite: true,
  };
}

function datePickerDateToISO(dateStr) {
  const [day, month, year] = dateStr.split('.');
  return `${year}-${month}-${day}`;
}