/**********************************************************/
/* @name Custom Select by VICTORY digital */
/* @copyright VICTORY group */
/* @support https://victoryagency.ru/digital */
/**********************************************************/
let vgSelectStarted = false;
export function initVgSelect() {
  if (vgSelectStarted) return;
  vgSelectStarted = true;
  /**
   * Утилита Debounce. Вызывает функцию только один раз после прекращения потока вызовов.
   * Это ключ к производительности при массовом добавлении элементов.
   * @param {Function} func Функция для вызова.
   * @param {number} wait Задержка в миллисекундах (50ms - идеальный баланс).
   */
  function debounce(func, wait) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }

  function initializeVGselect(nativeSelect) {
    // ЗАЩИТА: Проверяем, не был ли этот селект уже инициализирован
    if (nativeSelect.dataset.vgInitialized) {
      return;
    }
    nativeSelect.dataset.vgInitialized = 'true';

    // Vanilla JS кастомный select для всех .vgselect
    // (Вместо forEach, код теперь применяется к одному `nativeSelect`)

    // Скрываем оригинальный select
    nativeSelect.style.display = 'none';
    // Создаём кастомный контейнер
    const root = document.createElement('div');
    root.className = 'vgselect';
    nativeSelect.parentNode.insertBefore(root, nativeSelect);
    const useBody =
      nativeSelect.dataset.body !== undefined &&
      nativeSelect.dataset.body !== 'false';
    const placeholder = nativeSelect.getAttribute('placeholder');
    const dataPlaceholder = nativeSelect.getAttribute('data-placeholder');
    const search = nativeSelect.hasAttribute('data-search');
    // Получаем опции
    const options = Array.from(nativeSelect.options).map((opt) => ({
      value: opt.value,
      label: opt.textContent,
      checked: opt.selected,
      disabled: opt.disabled,
    }));
    let open = false;
    let searchValue = '';
    let menu = null;
    let menuAlign = '';
    let menuPos = {
      top: 0,
      left: 0,
      width: 0,
    };
    let optionsContainer = null; // контейнер с опциями
    let searchInput = null; // поле поиска
    const isMultiple = nativeSelect.multiple;
    let selected; // Объявляем переменную

    // Для мульти-селекта логика остается прежней
    if (isMultiple) {
      selected = options.filter((o) => o.checked);
    } else {
      // Ищем, есть ли в оригинальной разметке опция с атрибутом `selected`
      const hasExplicitlySelectedOption =
        nativeSelect.querySelector('option[selected]');

      // Если есть атрибут placeholder и НИ ОДНА опция не была выбрана явно...
      if (placeholder && !hasExplicitlySelectedOption) {
        // ...тогда мы должны показать плейсхолдер.
        selected = null;

        // --- КЛЮЧЕВОЕ ИЗМЕНЕНИЕ ---
        // Принудительно сбрасываем выбор в самом нативном элементе.
        // Это предотвратит первоначальную отрисовку первого элемента.
        nativeSelect.selectedIndex = -1;
      } else {
        // В противном случае (если что-то выбрано или плейсхолдера нет),
        // берём текущий выбранный элемент.
        selected = options.find((o) => o.checked) || null;
      }
    }
    // Кастомный UI
    let control = document.createElement('div');
    control.className = 'vgselect__control';
    control.tabIndex = 0;
    root.appendChild(control);

    function updateControl() {
      // Шаблон для значения, если что-то выбрано
      const selectedValueTemplate = (label) =>
        `${
          dataPlaceholder
            ? `<span class="vgselect__placeholder">${dataPlaceholder}</span>`
            : ''
        } <span class="vgselect__value">${label}</span>`;
      // Шаблон для плейсхолдера, если ничего не выбрано
      const placeholderTemplate = () => {
        if (dataPlaceholder) {
          return `<span class="vgselect__placeholder">${dataPlaceholder}</span><span class="vgselect__value">Ничего не выбрано</span>`;
        } else {
          return `<span class="vgselect__value">${
            placeholder || 'Ничего не выбрано'
          }</span>`;
        }
      };
      if (isMultiple) {
        root.classList.add('multiple');
        if (selected.length) {
          control.innerHTML = `${
            dataPlaceholder
              ? `<span class="vgselect__placeholder">${dataPlaceholder}</span>`
              : ''
          } <div class="vgselect__values"><div class="vgselect__nowrap">${selected
            .map((s) => s.label)
            .join(', ')}</div></div>`;
          control.classList.remove('placeholder');
        } else {
          control.innerHTML = placeholderTemplate();
          control.classList.add('placeholder');
        }
      } else {
        if (selected) {
          control.innerHTML = selectedValueTemplate(selected.label);
          control.classList.remove('placeholder');
        } else {
          control.innerHTML = placeholderTemplate();
          control.classList.add('placeholder');
        }
      }
    }
    updateControl();
    // Позиционирование меню
    function updateMenuPos() {
      const rect = control.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      const spaceBelow = viewportHeight - rect.bottom;
      const spaceAbove = rect.top;
      menuPos = {
        top: rect.bottom + window.scrollY,
        left: rect.left + window.scrollX,
        width: rect.width,
      };
      if (!menu) return;
      const maxHeight = Math.max(spaceBelow - 10, 150); // 10px отступ, минимум 150px
      menu.style.maxHeight = maxHeight + 'px';
      menu.style.overflowY = 'auto';
      if (useBody) {
        menu.style.position = 'absolute';
        menu.style.top = menuPos.top + 'px';
        menu.style.left =
          menuAlign === 'right' && menu.offsetWidth
            ? menuPos.left + menuPos.width - menu.offsetWidth + 'px'
            : menuPos.left + 'px';
        menu.style.minWidth = menuPos.width + 'px';
      } else {
        menu.style.position = 'absolute';
        menu.style.top = '';
        menu.style.left = '';
        menu.style.minWidth = '100%';
      }
    }

    function getMenuPosition() {
      if (!menu) return '';
      const menuRect = menu.getBoundingClientRect();
      const viewportWidth = window.innerWidth;
      if (menuRect.right > viewportWidth) return 'right';
      return '';
    }

    function openMenu() {
      if (open) return;
      open = true;
      root.classList.add('open');
      renderMenu();
      updateMenuPos();
      setTimeout(() => {
        menuAlign = getMenuPosition();
        if (!menu) return;
        if (menuAlign === 'right') menu.classList.add('align-right');
        else menu.classList.remove('align-right');
        updateMenuPos();
      }, 0);
      window.addEventListener('scroll', updateMenuPos, true);
      window.addEventListener('resize', updateMenuPos);
      document.addEventListener('mousedown', handleClickOutside);
      document.addEventListener('keydown', handleKeyDown);
    }

    function closeMenu() {
      if (!open) return;
      open = false;
      root.classList.remove('open');
      if (menu && menu.parentNode) menu.parentNode.removeChild(menu);
      menu = null;
      optionsContainer = null;
      searchInput = null;
      menuAlign = ''; // Очистка поиска
      if (search) {
        searchValue = '';
      }
      window.removeEventListener('scroll', updateMenuPos, true);
      window.removeEventListener('resize', updateMenuPos);
      document.removeEventListener('mousedown', handleClickOutside);
      document.removeEventListener('keydown', handleKeyDown);
    }

    function handleClickOutside(e) {
      if (
        !root.contains(e.target) &&
        (!menu || !menu.contains(e.target)) &&
        (!searchInput || !searchInput.contains(e.target))
      ) {
        closeMenu();
      }
    }

    function handleKeyDown(e) {
      if (!open || !menu) return;
      const optionDivs = menu.querySelectorAll('.vgselect__option');
      const current = document.activeElement;
      const list = Array.from(optionDivs);
      let index = list.findIndex((el) => el === current);
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        index = (index + 1) % list.length;
        list[index]?.focus();
      }
      if (e.key === 'ArrowUp') {
        e.preventDefault();
        index = (index - 1 + list.length) % list.length;
        list[index]?.focus();
      }
      if (
        e.key === 'Enter' ||
        e.code === 'Space' ||
        e.key === ' ' ||
        e.key === 'Spacebar'
      ) {
        if (current && current.classList.contains('vgselect__option')) {
          e.preventDefault();
          current.click();
        }
      }
      if (e.key === 'Escape' || e.key === 'Tab') {
        closeMenu();
      }
    }

    function getFilteredOptions() {
      if (search && searchValue.trim()) {
        return options.filter((opt) =>
          opt.label.toLowerCase().includes(searchValue.trim().toLowerCase())
        );
      }
      return options;
    }

    function renderMenu() {
      if (menu && menu.parentNode) menu.parentNode.removeChild(menu);
      menu = document.createElement('div');
      menu.className = 'vgselect__menu';
      menu.style.position = 'absolute'; // Поиск
      if (search) {
        const searchDiv = document.createElement('div');
        searchDiv.className = 'vgselect__search';
        searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.value = searchValue;
        searchInput.placeholder = 'Поиск...';
        setTimeout(() => {
          searchInput.focus();
        }, 0);
        searchInput.addEventListener('input', function (e) {
          searchValue = e.target.value;
          renderOptions(); // вместо renderMenu
          updateMenuPos(); // позиционирование
        });
        searchDiv.appendChild(searchInput);
        menu.appendChild(searchDiv);
      } // Контейнер опций
      optionsContainer = document.createElement('div');
      optionsContainer.className = 'vgselect__options';
      menu.appendChild(optionsContainer);
      if (useBody) {
        menu.style.zIndex = 9999;
        document.body.appendChild(menu);
      } else {
        menu.style.zIndex = 50;
        root.appendChild(menu);
      }
      renderOptions(); // рендерим сами опции
      // Автофокус на выбранной опции
      const optionDivs = menu.querySelectorAll('.vgselect__option');
      if (!isMultiple && selected) {
        const match = Array.from(optionDivs).find(
          (el) => el.textContent === selected.label
        );
        if (match) {
          match.focus();
        } else if (optionDivs[0]) {
          optionDivs[0].focus();
        }
      } else if (optionDivs[0]) {
        optionDivs[0].focus();
      }
      updateMenuPos();
    }

    function renderOptions() {
      if (!optionsContainer) return;
      optionsContainer.innerHTML = '';
      const filteredOptions = getFilteredOptions().filter(
        (opt) => !opt.disabled
      );
      if (filteredOptions.length === 0) {
        const noOpt = document.createElement('div');
        noOpt.className = 'vgselect__no-options';
        noOpt.textContent = 'Нет совпадений';
        optionsContainer.appendChild(noOpt);
      } else {
        filteredOptions.forEach(function (opt) {
          const isSelected = isMultiple
            ? selected.some((s) => s.value === opt.value)
            : selected && selected.value === opt.value;
          const optDiv = document.createElement('div');
          optDiv.className =
            'vgselect__option' + (isSelected ? ' selected' : '');
          optDiv.tabIndex = -1; // чтобы можно было фокусировать программно
          optDiv.textContent = opt.label;
          optDiv.addEventListener('mousedown', function (e) {
            if (e.button === 0) e.stopPropagation();
          });
          optDiv.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isMultiple) {
              const index = selected.findIndex((s) => s.value === opt.value);
              const isNowSelected = index !== -1;
              if (isNowSelected) {
                selected.splice(index, 1);
                optDiv.classList.remove('selected');
              } else {
                selected.push(opt);
                optDiv.classList.add('selected');
              } // Обновить native select
              Array.from(nativeSelect.options).forEach((o) => {
                o.selected = selected.some((s) => s.value === o.value);
              });
              nativeSelect.dispatchEvent(
                new Event('change', {
                  bubbles: true,
                })
              );
              updateControl();
            } else {
              selected = opt; // Снять выделение со всех
              const allOptionDivs =
                optionsContainer.querySelectorAll('.vgselect__option');
              allOptionDivs.forEach((div) => div.classList.remove('selected'));
              optDiv.classList.add('selected');
              nativeSelect.value = opt.value;
              nativeSelect.dispatchEvent(
                new Event('change', {
                  bubbles: true,
                })
              );
              closeMenu();
              searchValue = '';
              updateControl();
            }
          });
          optionsContainer.appendChild(optDiv);
        });
      }
    }
    control.addEventListener('focus', () => {
      root.classList.add('focus');
    });
    control.addEventListener('blur', () => {
      root.classList.remove('focus');
    });
    control.addEventListener('click', function (e) {
      // ✅ Только если клик реально по control, а не потомкам
      if (control.contains(e.target) && !menu?.contains(e.target)) {
        control.focus();
        if (open) closeMenu();
        else openMenu();
      }
    });
    control.addEventListener('keydown', function (e) {
      if (
        !open &&
        (e.code === 'Space' ||
          e.key === ' ' ||
          e.key === 'Spacebar' ||
          e.key === 'ArrowDown' ||
          e.key === 'ArrowUp')
      ) {
        e.preventDefault(); // обязательно, иначе страница прокручивается
        setTimeout(() => {
          openMenu();
        }, 0);
      }
    }); // Обработка клика по label
    const label = nativeSelect.closest('label');
    if (label) {
      label.addEventListener('click', (e) => {
        // Если клик по input или чему-то внутри меню — не вмешиваемся
        if (
          (searchInput && searchInput.contains(e.target)) ||
          (menu && menu.contains(e.target))
        ) {
          return;
        }
        e.preventDefault();
        control.focus();
      });
    }
    nativeSelect.addEventListener('change', function (e) {
      if (isMultiple) {
        const selectedValues = Array.from(nativeSelect.selectedOptions).map(
          (o) => o.value
        );
        selected = options.filter((opt) => selectedValues.includes(opt.value));
      } else {
        const val = nativeSelect.value;
        selected =
          options.find(function (o) {
            return o.value === val;
          }) || null;
      }
      updateControl();
    }); // Переопределяем value-сеттер
    const nativeValueProp = Object.getOwnPropertyDescriptor(
      HTMLSelectElement.prototype,
      'value'
    );
    Object.defineProperty(nativeSelect, 'value', {
      get: function () {
        return nativeValueProp.get.call(this);
      },
      set: function (val) {
        nativeValueProp.set.call(this, val);
        nativeSelect.dispatchEvent(
          new Event('change', {
            bubbles: true,
          })
        );
      },
    }); // Отслеживаем изменения selected/value у native select
    const observer = new MutationObserver(() => {
      // ПЕРЕСОБИРАЕМ options при изменении DOM
      options.length = 0;
      Array.from(nativeSelect.options).forEach((opt) =>
        options.push({
          value: opt.value,
          label: opt.textContent,
          checked: opt.selected,
          disabled: opt.disabled,
        })
      );
      if (isMultiple) {
        const selectedValues = Array.from(nativeSelect.selectedOptions).map(
          (o) => o.value
        );
        selected = options.filter((opt) => selectedValues.includes(opt.value));
      } else {
        selected =
          options.find((opt) => opt.value === nativeSelect.value) || null;
      }
      const selectClasses = nativeSelect.classList;
      selectClasses.forEach((cls) => {
        if (!root.classList.contains(cls)) {
          root.classList.add(cls);
        }
      });
      updateControl();
    }); // Запускаем наблюдение
    observer.observe(nativeSelect, {
      attributes: true,
      childList: true,
      subtree: true,
    }); // Обработка required
    const form = nativeSelect.closest('form');
    const validate = () => {
      if (
        (nativeSelect.required && !nativeSelect.value) ||
        nativeSelect.classList.contains('error')
      ) {
        root.classList.add('error');
      } else {
        if (!nativeSelect.classList.contains('error')) {
          root.classList.remove('error');
        }
      }
    }; // Проверка при потере фокуса
    nativeSelect.addEventListener('invalid', (e) => {
      e.preventDefault(); // отключаем стандартное сообщение браузера
      validate();
    });
    nativeSelect.addEventListener('blur', validate); // Проверка при отправке формы
    if (form) {
      form.addEventListener('submit', () => {
        validate();
      });
    }
  }

  /**
   * Функция, которая находит на странице все селекты, которые еще не были
   * инициализированы, и запускает для них ваш оригинальный скрипт.
   */
  function processNewSelects() {
    document
      .querySelectorAll('select.vgselect:not([data-vg-initialized])')
      .forEach(initializeVGselect);
  }

  // Создаем debounced-версию функции, чтобы она не запускалась слишком часто
  const debouncedProcessNewSelects = debounce(processNewSelects, 50);

  // Инициализируем селекты, которые уже есть на странице в момент загрузки скрипта
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', processNewSelects);
  } else {
    processNewSelects();
  }

  // Создаем наблюдатель за DOM, который будет отслеживать все изменения
  const observer = new MutationObserver((mutations) => {
    // Если были добавлены новые элементы, запускаем отложенную проверку
    for (const mutation of mutations) {
      if (mutation.addedNodes.length > 0) {
        debouncedProcessNewSelects();
        // Одного вызова достаточно, даже если изменений было много
        return;
      }
    }
  });

  // Запускаем наблюдение за всей страницей для полной автоматизации
  observer.observe(document.body, {
    childList: true, // Следить за добавлением/удалением дочерних узлов
    subtree: true, // Включая все дочерние уровни
  });
}
