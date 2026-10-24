document.addEventListener('DOMContentLoaded', function() {
  leadsPage.init();
});

class LeadsPage {
  ajaxTable;
  _savedData = {};

  init() {
    this.ajaxTable = new AjaxTable();
    this.ajaxTable.init({
      tableSelector: '#invoiceTable',
      updateSectionSelector: '#invoiceTableBody',
      limitsSelector: '#invoiceTableLimits',
      paginationSelector: '#invoiceTablePagination',
      fetch: window.location.href,
      method: 'POST',
      isLoadingClass: 'is-loading',
      apiAction: 'vg-mailer-get-forms',
      onFinally: () => {
        Modals.refreshAllButtons();
        const $viewFormButtons = document.querySelectorAll('.js-view-form');
        for (const $button of $viewFormButtons) {
          $button.addEventListener('click', () => {
            leadsPageViewOrder.updateViewInfo($button.dataset?.orderId);
          });
        }

        const $resetButton = document.querySelector('.js-filter-reset');
        if (this.ajaxTable.getActiveFilters().length > 0) {
          $resetButton.classList.remove('hidden');
        } else {
          $resetButton.classList.add('hidden');
        }

        const $repeatButtons = document.querySelectorAll('.js-repeat-form');
        for (const $repeatButton of $repeatButtons) {
          $repeatButton.addEventListener('click', () => {
            this.resendForm($repeatButton.dataset?.orderId);
          });
        }
      },
      onSuccess: (items) => {
        const rows = items?.data?.rows;
        if (!rows) {
          return;
        }

        for (const row of rows) {
          this._savedData[row.id] = row;
        }
      },

      renderRows: (item) => {
        return [
          `<td>
                <div class="text-nowrap">${item?.created_at ?? ''}</div>
              </td>`,
          `<td>
                <div class="text-nowrap">${this._renderPhones(
              item?.phones ?? '',
          )}</div>
              </td>`,
          `<td>${item?.source ?? ''}</td>`,
          `<td>${item?.form_name ?? ''}</td>`,
          `<td>${this.renderStatus(item?.status)}</td>`,
          `<td class="sticky-col">
                <div class="actions">
                    <button class="button--black button--icon js-view-form" data-order-id="${item?.id}" data-modal-id="viewOrder" title="Посмотреть детали">
                        <span class="icon icon-eye"></span>
                    </button>
                    <button class="button--black button--icon js-repeat-form" data-button-repeat data-order-id="${item?.id}" title="Отправить повторно">
                        <span class="icon icon-repeat"></span>
                    </button>
                </div>
            </td>`,
        ];
      },
      renderPaginationItem: (page) =>
          `<a class="page-number" data-page="${page}">${page}</a>`,
      renderPaginationItemCurrent: (page) =>
          `<span class="page-number current">${page}</span>`,
      renderPaginationPrev: (page) =>
          `<span class="button-prev" data-page="${page}"></span>`,
      renderPaginationPrevDisabled: () =>
          `<span class="button-prev disabled" aria-disabled="true""></span>`,
      renderPaginationNext: (page) =>
          `<span class="button-next" data-page="${page}"></span>`,
      renderPaginationNextDisabled: () =>
          `<span class="button-next disabled" aria-disabled="true"></span>`,
    });
    this._initFilters();
  }

  getFormById(id) {
    return this._savedData[id] ?? null;
  }

  _renderPhones(phones) {
    const response = [];
    const masked = new IMask.MaskedPattern(getPhoneMask());
    const phonesArray = phones.split(',');

    for (const phone of phonesArray) {
      if (phone !== '') {
        masked.resolve(phone);
        response.push(masked.value);
      }
    }

    return response.join(', ');
  }

  renderStatus(status, label = null) {
    switch (status) {
      case 'sent':
        return `<span class="status green">${label ?? 'Отправлен'}</span> `;
      case 'blocked':
        return `<span class="status orange">${label ?? 'Заблокирован'}</span> `;
      case 'error':
        return `<span class="status error">${label ?? 'Ошибка'}</span> `;
      default:
        return '';
    }
  }

  resendForm(formId) {
    _fetch('vg-mailer-form-resend', {
      formId,
    }, {
      onSuccess: () => {
        this.ajaxTable.submit();
        new VGalert({
          theme: 'light',
          text: 'Форма отправлена',
          classes: ['success'],
          closeButton: true,
        });
      },
    });
  }

  blockPhones(formId) {
    _fetch('vg-mailer-block-phones', {
      formId,
    }, {
      onSuccess: () => {
        new VGalert({
          theme: 'light',
          text: 'Номера заблокированы',
          classes: ['success'],
          closeButton: true,
        });
      },
    });
  }

  _initFilters() {
    const filterItems = [];
    const filterFields = ['phone', 'source', 'form_name'];

    // Обычные поля
    for (const key of filterFields) {
      const $filter = document.querySelector(`#${key}`);
      const filter = this.ajaxTable.filter(key, 'like', 300);

      $filter?.addEventListener('keyup', () => {
        const value = $filter.value;
        if (value === '') {
          filter.reset();
        } else {
          filter.update(value);
        }
      });
      if ($filter) {
        filterItems.push($filter);
      }
    }

    // Дата создания
    const datePicker = new DatePicker('.createdate');
    const createDateFilter = this.ajaxTable.filter('created_at', 'between');
    datePicker.onChange((data) => {
      const startDate = data?.startDate;
      const endDate = data?.endDate;

      if (!startDate || !endDate) {
        createDateFilter.reset();
        return;
      }

      createDateFilter.update({
        from: `${datePickerDateToISO(data?.startDate)}T00:00:00`,
        to: `${datePickerDateToISO(data?.endDate)}T23:59:59`,
      });
    });

    // Статусы
    const statusFilter = this.ajaxTable.filter('status', 'in', 300);
    const statusFilterSelector = '.js-filter-status input[type="checkbox"]';
    const $statuses = document.querySelectorAll(statusFilterSelector);
    for (const $status of $statuses) {
      $status.addEventListener('change', () => {
        const $activeItems = document.querySelectorAll(
            `${statusFilterSelector}:checked`,
        );
        const value = [];
        for (const $activeItem of $activeItems) {
          value.push($activeItem.value ?? 'unknown');
        }

        if (value.length > 0) {
          statusFilter.update(value);
        } else {
          statusFilter.reset();
        }
      });
    }

    // Сбросить фильтры
    document.querySelector('.js-filter-reset')?.
        addEventListener('click', (e) => {
          e.preventDefault();

          for (const $item of filterItems) {
            $item.value = '';
          }
          for (const $status of $statuses) {
            $status.checked = false;
          }

          datePicker.reset();
          this.ajaxTable.reset();
        });
  }
}

class LeadsPageViewOrder {
  _$repeatButton;
  _$blockNumberButton;

  constructor() {
    this._$repeatButton = document.querySelector('[data-button-repeat]');
    this._$blockNumberButton = document.querySelector('[data-button-block]');

    this._$repeatButton?.addEventListener('click', () => {
      leadsPage.resendForm(this._$repeatButton.dataset?.orderId);
    });
    this._$blockNumberButton?.addEventListener('click', () => {
      leadsPage.blockPhones(this._$blockNumberButton.dataset?.orderId);
    });
  }

  updateViewInfo(formId) {
    const $modal = Modals?.getModal('viewOrder')?.$modal ?? null;
    const data = leadsPage.getFormById(formId);

    console.log(data);

    if (!$modal || !data) {
      new VGalert({
        title: `Ошибка`,
        classes: ['error'],
        text: 'Что-то пошло не так',
      });
      return;
    }

    const $createdAt = document.querySelector('.js-created-at-field');
    const $formName = document.querySelector('.js-form_name-field');
    const $details = document.querySelector('.js-detail-fields');
    const $sendersStatuses = document.querySelector(
        '.js-senders-workers-statuses > .value',
    );
    const $sendersLogs = document.querySelector('.js-senders-logs');

    let detailsHTML = '';
    let sendersHTML = '';
    let sendersLogHTML = '';

    $createdAt.innerHTML = data.created_at ?? '';
    $formName.innerHTML = data.form_name ?? '';

    detailsHTML += this._renderDetailRow('Источник', data?.source ?? '');
    for (const item of data?.form_fields ?? []) {
      let value = item.value ?? '';
      if (typeof value !== 'string' && typeof value !== 'number') {
        value = 'Нет способа вывода';
      }
      detailsHTML += this._renderDetailRow(item.name, value);
    }
    $details.innerHTML = detailsHTML;

    for (const key of Object.keys(data?.senders_responses)) {
      const senderResponse = data.senders_responses[key];

      sendersHTML += leadsPage.renderStatus(
          senderResponse.status,
          senderResponse.label,
      );
      sendersLogHTML += this._renderLog(senderResponse);
    }
    $sendersStatuses.innerHTML = sendersHTML;
    $sendersLogs.innerHTML = `<div class="modal-logs">${sendersLogHTML}</div>`;

    this._$repeatButton.dataset.orderId = data.id;
    this._$blockNumberButton.dataset.orderId = data.id;
  }

  _renderDetailRow(label, value) {
    return `<div class="modal-field">
      <div class="title">${label}:</div>
      <div class="value">${value}</div>
    </div>`;
  }

  _renderLog(sender) {
    let log = sender.data;
    if (typeof log !== 'string' && typeof log !== 'number') {
      try {
        log = JSON.stringify(log);
      } catch (e) {
        console.error(e);
        log = 'Вывод такого лога не поддерживается';
      }
    }

    return `
      <div class="modal-logs__item">
        ${leadsPage.renderStatus(sender.status, sender.label)}    
        <code class="log">${log}</code>
      </div>
    `;
  }
}

const leadsPage = new LeadsPage();
const leadsPageViewOrder = new LeadsPageViewOrder();
