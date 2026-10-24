import { Pagination } from '../pagination/Pagination';

export function initPaginations(selector = '.pagination') {
  document.querySelectorAll(selector).forEach((el) => {
    if (el.__pagination) return;

    const options = {
      total: Number(el.dataset.total) || 1,
      pageSize: Number(el.dataset.size) || 10,
      currentPage: Number(el.dataset.page) || 1,
      siblings: Number(el.dataset.siblings) || 1,
    };

    const instance = new Pagination(el, options);
    el.__pagination = instance;
  });
}
