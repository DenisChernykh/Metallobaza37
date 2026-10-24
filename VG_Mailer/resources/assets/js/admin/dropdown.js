class Dropdown {
  constructor() {
    this.originalParents = new Map();
    this.dropdownOffsets = new Map(); // Для хранения исходных отступов
    this.scrollListeners = new Map();
    this.init();
  }

  init() {
    // карта «кнопка → блок»
    this.pairs = new Map();

    // Перемещаем все дропдауны в конец body
    document.querySelectorAll('[data-dropdown-id]').forEach((dropdownBlock) => {
      const id = dropdownBlock.dataset.dropdownId;
      const button = dropdownBlock.closest('.dropdown')?.
          querySelector(`[data-dropdown-block="${id}"]`);

      if (!button) return;

      // сохраняем пару
      this.pairs.set(button, dropdownBlock);

      // переносим в body
      document.body.appendChild(dropdownBlock);

      // вешаем обработчик (через замыкание dropdownBlock известен без поиска)
      button.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleDropdown(dropdownBlock, button);
      });

      // позиционируем один раз
      const pos =
          dropdownBlock.getAttribute('data-dropdown-position') || 'bottom';
      this.setupPosition(dropdownBlock, button, pos);
    });

    window.addEventListener('resize', () => this.updateAllDropdownPositions());
    window.addEventListener('scroll', () => this.updateAllDropdownPositions());

    document.addEventListener('click', (event) => {
      this.closeAllDropdowns(event);
    });
  }

  setupPosition(dropdownBlock, button, position) {
    dropdownBlock.style.position = 'fixed';
    dropdownBlock.style.zIndex = '1000';
    // При первом открытии проверяем границы и сохраняем положение
    this.updateDropdownPosition(dropdownBlock, button, position, true, true);
  }

  fitsViewport(rect, gap = 0) {
    return (
        rect.left >= gap &&
        rect.top >= gap &&
        rect.right <= window.innerWidth - gap &&
        rect.bottom <= window.innerHeight - gap
    );
  }

  updateDropdownPosition(
      dropdownBlock,
      button,
      position,
      checkBounds = false,
      isInitial = false,
  ) {
    const buttonRect = button.getBoundingClientRect();
    const dropdownRect = dropdownBlock.getBoundingClientRect();
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    const gap = 0.625 * getRem(); // чуть «видимее» -- 0.625em

    let left,
        top,
        transform = 'none';

    /* === базовое позиционирование, как раньше === */
    switch (position) {
      case 'top':
        left = buttonRect.left + buttonRect.width / 2 - dropdownRect.width / 2;
        top = buttonRect.top - gap;
        transform = 'translateY(-100%)';
        break;
      case 'bottom':
        left = buttonRect.left + buttonRect.width / 2 - dropdownRect.width / 2;
        top = buttonRect.bottom + gap;
        break;
      case 'left':
        left = buttonRect.left - gap;
        top = buttonRect.top + buttonRect.height / 2 - dropdownRect.height / 2;
        transform = 'translateX(-100%)';
        break;
      default: // "right"
        left = buttonRect.right + gap;
        top = buttonRect.top + buttonRect.height / 2 - dropdownRect.height / 2;
    }

    /* === Поправка на края === */
    if (checkBounds) {
      // сдвигаем внутрь если чут-чуть вылез
      left = Math.min(Math.max(left, gap), vw - dropdownRect.width - gap);
      top = Math.min(Math.max(top, gap), vh - dropdownRect.height - gap);

      /* === Главное новое правило: НЕ ВЛЕЗЛО → по центру === */
      const virtualRect = {
        left,
        top,
        right: left + dropdownRect.width,
        bottom: top + dropdownRect.height,
      };
      if (!this.fitsViewport(virtualRect, gap)) {
        left = (vw - dropdownRect.width) / 2;
        top = (vh - dropdownRect.height) / 2;
        transform = 'none';
      }
    }

    dropdownBlock.style.left = `${left}px`;
    dropdownBlock.style.top = `${top}px`;
    dropdownBlock.style.transform = transform;
  }

  updateAllDropdownPositions() {
    // перебираем только активные пары
    for (const [button, block] of this.pairs) {
      if (block.classList.contains('active')) {
        const pos = block.getAttribute('data-dropdown-position') || 'bottom';
        this.updateDropdownPosition(block, button, pos);
      }
    }
  }

  toggleDropdown(dropdownBlock, button) {
    const opening = !dropdownBlock.classList.contains('active');
    const position =
        dropdownBlock.getAttribute('data-dropdown-position') || 'bottom';

    button.classList.toggle('active');

    if (opening) {
      this.updateDropdownPosition(dropdownBlock, button, position, true, true);
      this.openDropdown(dropdownBlock);

      /* === привязываем к скролл-контейнеру === */
      const scrollParent = this.getScrollableParent(button);
      if (scrollParent) {
        const handler = () =>
            this.updateDropdownPosition(dropdownBlock, button, position);
        scrollParent.addEventListener('scroll', handler, {passive: true});
        this.scrollListeners.set(dropdownBlock, {scrollParent, handler});
      }
    } else {
      this.closeDropdown(dropdownBlock);
      /* убираем listener, если был */
      const saved = this.scrollListeners.get(dropdownBlock);
      if (saved) {
        saved.scrollParent.removeEventListener('scroll', saved.handler);
        this.scrollListeners.delete(dropdownBlock);
      }
    }
  }

  openDropdown(dropdownBlock) {
    dropdownBlock.style.height = '0';
    dropdownBlock.classList.add('active');
    const height = dropdownBlock.scrollHeight + 'px';
    dropdownBlock.style.height = height;

    // Добавляем фокус на первый элемент ввода
    const firstInput = dropdownBlock.querySelector('input, textarea, select');
    if (firstInput) {
      // Используем setTimeout чтобы дать время на анимацию открытия
      setTimeout(() => {
        firstInput.focus();
      }, 50);
    }

    dropdownBlock.addEventListener(
        'transitionend',
        () => {
          dropdownBlock.style.height = '';
        },
        {once: true},
    );
  }

  closeDropdown(dropdownBlock) {
    dropdownBlock.style.height = dropdownBlock.scrollHeight + 'px';

    // Используем requestAnimationFrame для плавного перехода
    requestAnimationFrame(() => {
      dropdownBlock.classList.remove('active');
      dropdownBlock.style.height = '0';
    });

    dropdownBlock.addEventListener(
        'transitionend',
        () => {
          dropdownBlock.style.height = '';
        },
        {once: true},
    );
  }

  closeAllDropdowns(event) {
    for (const [button, block] of this.pairs) {
      if (
          block.classList.contains('active') &&
          !block.contains(event.target) &&
          !button.contains(event.target)
      ) {
        this.closeDropdown(block);
        button.classList.remove('active');
      }
    }
  }

  getScrollableParent(element) {
    let parent = element.parentElement;
    while (parent) {
      const overflowY = window.getComputedStyle(parent).overflowY;
      if (overflowY === 'auto' || overflowY === 'scroll') {
        return parent;
      }
      parent = parent.parentElement;
    }
    return null;
  }
}

// Инициализируем все Dropdown
const dropdown = new Dropdown();

// Если нужно вызвать скриптом какой-то dropdown
// dropdown.toggleDropdown('date');

function getRem() {
  return parseFloat(getComputedStyle(document.documentElement).fontSize);
}
