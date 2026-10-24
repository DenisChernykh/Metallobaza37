export function initClearInput() {
  const input = document.querySelector('.js-search-input');

  const clearBtn = document.querySelector('.js-search-clear');

  if (!input || !clearBtn) return;

  input.addEventListener('input', () => {
    clearBtn.style.display = input.value ? 'inline-flex' : 'none';
  });

  clearBtn.addEventListener('click', () => {
    input.value = '';
    clearBtn.style.display = 'none';
    input.focus();
  });
}
