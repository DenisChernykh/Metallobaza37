export function initSort() {
  initCatalogSort();
}
function initCatalogSort() {
  const select = document.querySelector('.select-catalog');
  if (!select) return;
  
  try {
    const currentUrl = new URL(window.location.href);
    const curSort = currentUrl.searchParams.get('sort');
    const curOrder = (currentUrl.searchParams.get('order') || '').toUpperCase();
    if (curSort && curOrder) {
      const match = Array.from(select.options).find((opt) => {
        const p = new URLSearchParams(opt.value);
        return (
          p.get('sort') === curSort &&
          (p.get('order') || '').toUpperCase() === curOrder
        );
      });
      if (match) {
        select.value = match.value;
      }
    }
  } catch (e) {
    // no-op
  }

  select?.addEventListener('change', () => {
    const value = select.value;
    if (!value) return;

    const url = new URL(window.location.href);
    const params = new URLSearchParams(value);

    for (const [key, val] of params.entries()) {
      url.searchParams.set(key, val);
    }

    url.searchParams.delete('page');

    window.location.href = url.toString();
  });
}
