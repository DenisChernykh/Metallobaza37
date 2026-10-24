document.addEventListener('DOMContentLoaded', () => {
  phonePage.init();
});

class PhonePage {
  phonePageForm = new PhonePageForm();
  phonePageTable = new PhonePageTable();

  init() {
    this.phonePageTable.init();
    this.phonePageForm.init();
  }
}

class PhonePageTable {
  ajaxTable;

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
      apiAction: 'vg-mailer-get-blocked-phones',

      onFinally: () => {
        this._listenUpdateStatuses();
        this._listenDeleteButton();
        dropdown.init();

        const $resetButton = document.querySelector('.js-filter-reset');
        if (this.ajaxTable.getActiveFilters().length > 0) {
          $resetButton.classList.remove('hidden');
        } else {
          $resetButton.classList.add('hidden');
        }
      },

      renderRows: (item) => {
        return [
          `<td> 
            <div class="text-nowrap">${item?.created_at ?? ''}</div>
          </td>`,
          `<td>  
            <div class="text-nowrap">${this._preparePhoneView(
              item?.phone,
          )}</div>
          </td>`,
          `<td>
            <div class="dropdown"> 
              <div class="status green" data-dropdown-block="${
              item?.id ?? ''
          }" >
              <span>Детали</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="18rem" height="18rem" viewBox="0 0 18 18" fill="none">
                <path d="M18 8.94295V9.05721C17.9848 9.19502 17.9352 9.31971 17.8513 9.43127C16.7541 10.8899 15.416 12.1474 13.8509 13.0415C12.1929 13.9886 10.3011 14.4702 8.37633 14.3401C6.88676 14.2396 5.43516 13.7776 4.14914 13.0404C2.57062 12.1355 1.24066 10.8776 0.146602 9.42459C0.0652734 9.31678 0.0164063 9.19409 0 9.05651V8.94155C0.0152344 8.80467 0.0597656 8.68678 0.133594 8.58787C1.71246 6.47358 3.88617 4.69995 6.46418 3.98768C8.14723 3.52268 9.82723 3.51975 11.5042 3.97889C13.1706 4.43522 14.7076 5.34963 15.994 6.52385C16.7044 7.17237 17.3307 7.86413 17.8731 8.59913C17.9415 8.69217 17.9838 8.80678 18 8.94295ZM12.7811 8.99885C12.7811 7.99587 12.3826 7.03396 11.6734 6.32474C10.9642 5.61553 10.0023 5.21709 8.9993 5.21709C7.99631 5.21709 7.03441 5.61553 6.32519 6.32474C5.61597 7.03396 5.21754 7.99587 5.21754 8.99885C5.21754 10.0018 5.61597 10.9637 6.32519 11.673C7.03441 12.3822 7.99631 12.7806 8.9993 12.7806C10.0023 12.7806 10.9642 12.3822 11.6734 11.673C12.3826 10.9637 12.7811 10.0018 12.7811 8.99885Z" fill="white" />
                <path d="M10.3944 10.4799C11.2147 9.70954 11.2565 8.42138 10.4878 7.60272C9.71899 6.78407 8.43074 6.74492 7.61039 7.51528C6.79003 8.28565 6.74821 9.57381 7.51698 10.3925C8.28576 11.2111 9.574 11.2503 10.3944 10.4799Z" fill="white" />
              </svg>
            </div> 
            <div  
                class="dropdown__block js-listen-update-statuses" 
                data-dropdown-id="${item?.id ?? ''}"
              >  
              <div class="checkbox-group">
                ${this._renderServices(item?.services ?? {})}
              </div>
            </div>
          </td>`,
          `<td class="sticky-col">
            <div class="actions">
              <button data-row-id="${item?.id}" class="button--danger button--icon js-delete-row">
                <span class="icon icon-trash"></span>
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

  _initFilters() {
    const $phone = document.querySelector('#nomer');
    const phoneFilter = this.ajaxTable.filter('phone', 'like', 300);
    $phone.addEventListener('input', () => {
      phoneFilter.update($phone.value);
    });

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

    // Сбросить фильтры
    document.querySelector('.js-filter-reset')?.
        addEventListener('click', (e) => {
          e.preventDefault();

          $phone.value = '';
          datePicker.reset();
          this.ajaxTable.reset();
        });
  }

  _preparePhoneView(data) {
    const masked = new IMask.MaskedPattern(getPhoneMask());
    masked.resolve(data ?? '');
    return masked.value;
  }

  _renderServices(blockedData) {
    const result = [];

    for (const index of Object.keys(blockedData)) {
      const item = blockedData[index];

      result.push(`
        <label class="checkbox">
          <input 
            type="checkbox" 
            name="service[]" 
            value="${item.namespace}" 
            ${item.active && 'checked'} 
          >
          <span>${item.label}</span>
        </label>`);
    }

    return result.join('');
  }

  _listenUpdateStatuses() {
    const $blocks = document.querySelectorAll('.js-listen-update-statuses');

    const send = (id, $checkboxes) => {
      const services = [];

      for (const $checkbox of $checkboxes) {
        if ($checkbox.checked) {
          services.push($checkbox.value);
        }
      }

      _fetch(
          'vg-mailer-update-services',
          {id, services},
          {
            onSuccess: () => {
              new VGalert({
                title: 'Обновлено',
                classes: ['success'],
                theme: 'light',
                closeButton: true,
              });
            },
          },
      );
    };

    for (const $block of $blocks) {
      const id = $block.dataset?.dropdownId ?? -1;
      const $checkboxes = $block.querySelectorAll('input[type="checkbox"]');

      for (const $checkbox of $checkboxes) {
        $checkbox.addEventListener('change', () => {
          send(id, $checkboxes);
        });
      }
    }
  }

  _listenDeleteButton() {
    const $buttons = document.querySelectorAll('.js-delete-row');
    for (const $button of $buttons) {
      $button.addEventListener('click', (e) => {
        e.preventDefault();

        new VGalert({
          type: 'confirm',
          theme: 'light',
          position: 'center',
          title: 'Подтверждение',
          text: 'Вы уверены, что хотите удалить номер?',
          confirmButtonText: 'Да, продолжить',
          cancelButtonText: 'Нет, отмена',
          closeButton: true,
          onConfirm: () => {
            const id = $button.dataset.rowId ?? null;
            if (!id) {
              return;
            }

            _fetch(
                'vg-mailer-delete-blocked-phone',
                {
                  id,
                },
                {
                  onSuccess: () => {
                    phonePage.phonePageTable.ajaxTable.submit();
                    new VGalert({
                      theme: 'light',
                      text: 'Номер телефона удален',
                      classes: ['success'],
                      closeButton: true,
                    });
                  },
                },
            );
          },
          onCancel: () => {

          },
        });

        // if (!window.confirm("Вы уверены?")) {
        //   return;
        // }
      });
    }
  }
}

class PhonePageForm {
  _$phoneInput;
  _$submitButton;
  _IMaskPhoneInput;

  init() {
    this._$phoneInput = document.querySelector('#phoneToBlock');
    this._$submitButton = document.querySelector('#blockButton');
    this._IMaskPhoneInput = IMask(this._$phoneInput, getPhoneMask());

    this._$submitButton.addEventListener('click', (e) => {
      e.preventDefault();
      this._send();
    });
  }

  _send() {
    const $services = document.querySelectorAll(
        '#servicesToBlock [name="service[]"]',
    );

    _fetch(
        'vg-mailer-block-phone',
        {
          phone: this._$phoneInput.value,
          services: Array.from($services).
              filter((el) => el.checked).
              map((el) => el.value),
        },
        {
          onSuccess: () => {
            phonePage.phonePageTable.ajaxTable.submit();

            this._$phoneInput.value = '';
            this._IMaskPhoneInput.updateValue();

            new VGalert({
              theme: 'light',
              closeButton: true,
              title: 'Успешно',
              text: 'Телефон добавлен в список заблокированных',
              classes: ['success'],
            });
          },
        },
    );
  }
}

const phonePage = new PhonePage();
