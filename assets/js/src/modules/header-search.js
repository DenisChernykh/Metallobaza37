export function initHeaderSearch() {
  const btn = document.querySelector('.search-mobile');
  const search = document.querySelector('.header-search');
  const overlay = document.querySelector('.site-overlay');
  if (!btn || !search || !overlay) return;

  const input =
    document.querySelector('.js-search-input');
  const clearBtn =
    document.querySelector('.js-search-clear');

  const isOpen = () => search.classList.contains('is-open');

  const updateClear = () => {
    if (!input || !clearBtn) return;
    clearBtn.style.display = input.value ? 'inline-flex' : 'none';
  };

  const open = () => {
    search.classList.add('is-open');
    overlay.classList.add('is-visible');
    document.documentElement.classList.add('scroll-lock');
    input?.focus();
    updateClear();
  };

  const close = () => {
    search.classList.remove('is-open');
    overlay.classList.remove('is-visible');
    document.documentElement.classList.remove('scroll-lock');
  };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    isOpen() ? close() : open();
  });


  document.addEventListener('pointerdown', (e) => {
    if (!isOpen()) return;
    const t = e.target;
    if (search.contains(t) || btn.contains(t)) return;
    close();
  });


  input?.addEventListener('input', updateClear);


  clearBtn?.addEventListener('click', () => {
    if (!input) return;
    input.value = '';
    updateClear();
    input.focus();
  });


  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen()) close();
  });


  updateClear();
}
