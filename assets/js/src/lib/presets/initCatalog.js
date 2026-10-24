export function initCatalog() {
  const paginationEl = document.querySelector('.pagination');
  if (paginationEl) return;

  const pagination = paginationEl.__pagination;
  const list = document.querySelector('.catalog__list');

  paginationEl.addEventListener('click', (e) => {
    const { page, pageSize } = e.detail;

    pagination.setLoading(true);

    fetch(`/api/products?page=${page}&pageSize=${pageSize}`)
      .then((response) => response.json())
      .then(({ items, totalItems }) => {
        list.innerHTML = items.map((item) => `<li>${item.name}</li>`).join('');
        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
        pagination.setTotal(totalPages);
      })
      .finally(() => pagination.setLoading(false));
  });
}
