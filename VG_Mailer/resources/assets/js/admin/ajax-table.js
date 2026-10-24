/**
 * Инициализация проекта
 * @param {Object} configs
 * @param {string} configs.fetch - URL запроса
 * @param {string} configs.method - HTTP метод
 * @param {boolean} configs.debug - Включить отладку
 * @param {Object<string, string>} configs.headers - Заголовки
 *
 * Селекторы
 * @param {string} configs.tableSelector - Селектор таблицы
 * @param {string} configs.updateSectionSelector - Селектор секции для обновления
 * @param {string} configs.limitsSelector - Селектор секции для лимитов. Внутри будет искаться [data-limit],
 * на активный limit будет вешаться класс .active
 * @param {string} configs.paginationSelector - Селектор секции для пагинации
 *
 * @param {string|null} configs.isLoadingClass - Класс загрузки. Навешивается на таблицу во время fetch
 *
 * @param {function(): void} configs.onBeforeFetch - Вызывается перед fetch
 * @param {function({data: Object[], total: number}): {data: Object[], total: number}|void} configs.onSuccess - Успешный ответ
 * @param {function(any): void} configs.onError - Ошибка запроса
 * @param {function(): void} configs.onFinally - Вызывается всегда
 *
 * @param {function(Object): string} configs.renderColumn - render колонки
 * @param {function(Object): string[]|string} configs.renderColumn - render строк
 *
 * Пагинация. В любом конфиги пагинации можно выставить false, что бы не отображалось
 * @param {function(page): string|boolean} configs.renderPaginationItem - страница пагинации
 * @param {function(page): string|boolean} configs.renderPaginationItemCurrent - текущая страница
 * @param {function(): string|boolean} configs.renderPaginationEllipsis - если вкратце, то это ... между страницами
 * @param {function(page): string|boolean} configs.renderPaginationPrev - пред. страница
 * @param {function(): string|boolean} configs.renderPaginationPrevDisabled - если нет пред. страницы
 * @param {function(page): string|boolean} configs.renderPaginationNext - след. страница
 * @param {function(): string|boolean} configs.renderPaginationNextDisabled - если нет след. страницы
 */
class AjaxTable {
  _configs = {};

  /**
   * @type {{field: string, operation: string, value: any}[]}
   * @private
   */
  _activeFilters = [];
  /**
   * @type {[field: string, direction: 'asc'|'desc']}
   * @private
   */
  _activeSorting = [];
  _page = 1;
  _limit = 15;

  _$table;
  _$updateSection;
  _$pagination;

  _last_request = null;
  _fetchedItems;

  constructor() {
    this.reset = this.reset.bind(this);
  }

  init(configs) {
    const defaults = {
      fetch: '',
      method: 'POST',
      apiAction: '',
      debug: false,
      headers: {},
      tableSelector: '',
      updateSectionSelector: '',
      limitsSelector: '',
      paginationSelector: '',
      isLoadingClass: null,
      onBeforeFetch: null,
      onSuccess: null,
      onError: null,
      onFinally: null,
      renderColumn: (items) => `<tr>${items}</tr>`,
      renderRows: (item) => {
        return [
          `<td class="text-nowrap">${item?.created_at ?? '¯\\_(ツ)_/¯'}</td>`,
          `<td class="text-nowrap">${item?.phone ?? '¯\\_(ツ)_/¯'}</td>`,
          `<td>${item?.source ?? '¯\\_(ツ)_/¯'}</td>`,
          `<td>${item?.form_name ?? '¯\\_(ツ)_/¯'}</td>`,
        ];
      },
      renderPaginationItem: (page) =>
          `<div data-page="${page}">${page}</div>`,
      renderPaginationItemCurrent: (page) =>
          `<div class="active" data-page="${page}">${page}</div>`,
      renderPaginationEllipsis: () => `<div>...</div>`,
      renderPaginationPrev: (page) => `<div data-page="${page}">«</div>`,
      renderPaginationPrevDisabled: () => `<div class="disabled">«</div>`,
      renderPaginationNext: (page) => `<div data-page="${page}">»</div>`,
      renderPaginationNextDisabled: () => `<div class="disabled">»</div>`,
    };

    this._configs = {...defaults, ...configs};
    this._debug('Конфиги', this._configs);

    this._requiredConfigs();
    this._initHTMLBlock();
    this._initLimits();
  }

  /**
   * работа с одним фильтром
   * @param {string} field
   * @param {string} operation
   * @param {number} delay
   * @returns {{update: (string) => {}, reset: () => {}}}
   */
  filter(field, operation = 'eq', delay = 0) {
    let currentOperation = operation;
    let timeoutId = null;

    const removeByField = (f) => {
      this._activeFilters = this._activeFilters.filter(
          (filter) => filter.field !== f,
      );
    };
    const addByField = (f, v) => {
      removeByField(f);
      if (Array.isArray(v) && v.length === 0) {
        return;
      }

      this._activeFilters.push({
        field: f,
        operation: currentOperation,
        value: v,
      });
    };

    return {
      update: (value) => {
        addByField(field, value);
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
          this.submit();
        }, delay);
      },
      reset: () => {
        removeByField(field);
        this.submit();
      },
      operation: (operation) => {
        currentOperation = operation;
      },
    };
  }

  sort(field, direction = 'asc', delay = 0) {
    let currentDirection = direction;
    let timeoutId = null;

    const removeByField = (f) => {
      this._activeSorting = this._activeSorting.filter(
          (filter) => filter.field !== f,
      );
    };
    const addByField = (f) => {
      this._activeSorting = [
        {
          field: f,
          direction: currentDirection,
        },
      ];
    };

    return {
      add: () => {
        addByField(field);
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
          this.submit();
        }, delay);
      },
      remove: () => {
        removeByField(field);
        this.submit();
      },
      direction: (direction) => {
        currentDirection = direction;
      },
    };
  }

  submit() {
    const body = dataToFormData({
      action: this._configs.apiAction,
      filters: this._activeFilters,
      sorts: this._activeSorting,
      page: this._page,
      limit: this._limit,
    });

    if (body === this._last_request) {
      return;
    }

    this._last_request = body;
    this._debug('request', {
      activeFilters: this._activeFilters,
      activeSorting: this._activeSorting,
      page: this._page,
      limit: this._limit,
    });

    this.isLoading(true);
    this._configs.onBeforeFetch?.();

    fetch(this._configs.fetch, {
      method: this._configs.method,
      headers: {
        ...this._configs.headers,
      },
      body: body,
    }).then((response) => response.json()).then(async (data) => {
      if (!data.success) {
        throw new Error(data?.message || 'Server Error');
      }

      this._debug('response', data);
      this._fetchedItems = data;
      const newData = this._configs.onSuccess?.(data);
      await this._render(newData ?? data);
    }).catch((error) => {
      this._debug('error', error, 'red', true);
      new VGalert({
        theme: 'light',
        title: 'Ошибка',
        text: error.message,
        classes: ['error'],
      });
      this._configs.onError?.(error);
    }).finally(() => {
      this._configs.onFinally?.();
      this.isLoading(false);
    });
  }

  /**
   * сброс всех фильтром и сортировок
   */
  reset() {
    this._activeFilters = [];
    this._activeSorting = [];
    this.submit();
  }

  isLoading(bool) {
    const isLoadingClass = this._configs.isLoadingClass;
    if (!isLoadingClass) {
      return;
    }
    this._$table.classList[bool ? 'add' : 'remove'](isLoadingClass);
  }

  getActiveFilters() {
    return this._activeFilters;
  }

  _initLimits() {
    if (!this._configs.limitsSelector) {
      return;
    }

    const $block = document.querySelector(this._configs.limitsSelector);
    $block?.addEventListener('change', () => {
      this._limit = $block.value;
      this._page = 1;
      this.submit();
    });

    this._limit = $block.value;
    this._page = 1;
    this.submit();
  }

  /**
   * Проверка конфигов.
   * @returns {boolean}
   * @private
   */
  _requiredConfigs() {
    const requiredConfigs = [
      'fetch',
      'tableSelector',
      'updateSectionSelector',
    ];
    for (const config of requiredConfigs) {
      if (!this._configs[config]) {
        this._debug(`Config ${config} required.`, null, 'red', true);
        return false;
      }
    }
  }

  _initHTMLBlock() {
    const blocksData = [
      ['_$table', this._configs.tableSelector],
      ['_$updateSection', this._configs.updateSectionSelector],
      ['_$pagination', this._configs.paginationSelector],
    ];

    for (const block of blocksData) {
      if (!block[1]) {
        this._debug(
            `Selector for ${block} is empty`,
            null,
            'red',
            true,
        );
      }

      this[block[0]] = document.querySelector(block[1]);
      if (!this[block[0]]) {
        this._debug(`Block ${block} not found`, null, 'red', true);
      }
    }
  }

  _initPagination() {
    const $pages = this._$pagination?.querySelectorAll('[data-page]');
    if (!$pages) {
      return;
    }

    for (const $page of $pages) {
      $page.addEventListener('click', () => {
        this._page = Number($page.dataset.page);
        this.submit();
      });
    }
  }

  /**
   * @param {{data: Record<string, any>, total: number}} response
   * @private
   */
  _render(response) {
    this._renderColumns(response?.data.rows ?? null);
    this._renderPagination(response?.data.total ?? null);
  }

  _renderColumns(items) {
    if (!items) {
      this._debug('No items found.');
      this._$updateSection.innerHTML = '';
      return;
    }

    const rows = [];
    for (const item of items) {
      rows.push(this._configs.renderRows(item));
    }

    const columns = [];
    for (const row of rows) {
      const rowStr = Array.isArray(row) ? row.join('') : row;
      columns.push(this._configs.renderColumn(rowStr, items));
    }
    this._$updateSection.innerHTML = columns.join('');
  }

  _renderPagination(total) {
    if (!total) {
      this._debug('No total found.');
      this._$pagination.innerHTML = '';
      return;
    }

    const pages = Math.ceil(total / this._limit);
    if (pages === 1) {
      this._$pagination.innerHTML = '';
      return;
    }

    const items = [];

    const fnItem =
        typeof this._configs.renderPaginationItem === 'function' &&
        this._configs.renderPaginationItem;
    const fnItemCurrent =
        typeof this._configs.renderPaginationItemCurrent === 'function' &&
        this._configs.renderPaginationItemCurrent;
    const fnPrev =
        typeof this._configs.renderPaginationPrev === 'function' &&
        this._configs.renderPaginationPrev;
    const fnPrevDisabled =
        typeof this._configs.renderPaginationPrevDisabled === 'function' &&
        this._configs.renderPaginationPrevDisabled;
    const fnNext =
        typeof this._configs.renderPaginationNext === 'function' &&
        this._configs.renderPaginationNext;
    const fnNextDisabled =
        typeof this._configs.renderPaginationNextDisabled === 'function' &&
        this._configs.renderPaginationNextDisabled;
    const fnEllipsis =
        typeof this._configs.renderPaginationEllipsis === 'function' &&
        this._configs.renderPaginationEllipsis;

    // prev page
    if (this._page !== 1) {
      if (fnPrev) {
        items.push(fnPrev(this._page - 1));
      }
    } else if (fnPrevDisabled) {
      items.push(fnPrevDisabled());
    }

    for (let i = 1; i <= pages; i++) {
      const currentPage = Number(this._page);
      const isSkipPrev = i < currentPage - 1 && i !== 1;
      const isSkipNext = i > currentPage + 1 && i !== pages;

      if (fnEllipsis && (isSkipPrev || isSkipNext)) {
        if (i === currentPage - 2 || i === currentPage + 2) {
          items.push(fnEllipsis());
        }
        continue;
      }

      if (fnItemCurrent && currentPage === i) {
        items.push(fnItemCurrent(i));
      } else if (fnItem) {
        items.push(fnItem(i));
      }
    }

    // next page
    if (pages !== this._page) {
      if (fnNext) {
        items.push(fnNext(this._page + 1));
      }
    } else if (fnNextDisabled) {
      items.push(fnNextDisabled());
    }

    this._$pagination.innerHTML = items.join('');
    this._initPagination();
  }

  _debug(message, data = null, color = 'green', hard = false) {
    if (this._configs.debug || hard) {
      console.log(`%clog (${message})`, `color: ${color}`);
      if (data) {
        console.info(data);
      }
    }
  }

}
