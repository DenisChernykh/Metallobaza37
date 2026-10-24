export class Pagination {
  constructor(
    root,
    { total = 1, pageSize = 10, currentPage = 1, siblings = 1, onChange } = {}
  ) {
    this.root = root;
    this.total = Math.max(1, Number(total) || 1);
    this.pageSize = pageSize;
    this.currentPage = clamp(currentPage, 1, this.total);
    this.siblings = siblings;
    this.onChange = onChange;

    this.prevButton = this.root.querySelector('.pagination__arrow--prev');
    this.nextButton = this.root.querySelector('.pagination__arrow--next');
    this.list = this.root.querySelector('.pagination__list') || root;

    this.handleNumberClick = this.handleNumberClick.bind(this);

    this.list.addEventListener('click', this.handleNumberClick);
    this.prevButton?.addEventListener('click', (e) => {
      e.preventDefault();
      this.goTo(this.currentPage - 1);
    });
    this.nextButton?.addEventListener('click', (e) => {
      e.preventDefault();
      this.goTo(this.currentPage + 1);
    });

    this.render();
    this.emit();
  }

  goTo(page) {
    const next = clamp(page, 1, this.total);
    if (next === this.currentPage) return;
    this.currentPage = next;
    this.render();
    this.emit();
  }
  setTotal(totalPages) {
    const nextTotal = Math.max(1, Number(totalPages) || 1);
    if (nextTotal === this.total) return;
    this.total = nextTotal;
    this.currentPage = clamp(this.currentPage, 1, this.total);
    this.render();
  }
  handleNumberClick(event) {
    const link = event.target.closest('.pagination__link');
    if (!link) return;
    if (link.classList.contains('pagination__link--all')) return;
    event.preventDefault();

    const page = Number(link.textContent.trim());
    if (Number.isFinite(page)) this.goTo(page);
  }

  render() {
    const items = this.list.querySelectorAll(
      '.pagination__item, .pagination__link'
    );
    items.forEach((el) => el.remove());

    const tokens = buildTokens(this.currentPage, this.total, this.siblings);

    tokens.forEach((token) => {
      const li = document.createElement('li');
      li.className = 'pagination__item';
      if (token === '...') {
        const span = document.createElement('span');
        span.className = 'pagination__link pagination__link--all';
        span.textContent = '...';
        span.setAttribute('aria-hidden', 'true');
        span.tabIndex = -1;
        li.appendChild(span);
      } else {
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'pagination__link';
        a.textContent = String(token);
        if (token === this.currentPage) {
          a.classList.add('pagination__link--active');
          a.setAttribute('aria-current', 'page');
        }
        li.appendChild(a);
      }
      this.list.appendChild(li);
    });
    if (this.prevButton) this.prevButton.disabled = this.currentPage <= 1;
    if (this.nextButton)
      this.nextButton.disabled = this.currentPage >= this.total;
  }
  emit() {
    const meta = {
      page: this.currentPage,
      pageSize: this.pageSize,
      total: this.total,
      offset: (this.currentPage - 1) * this.pageSize,
    };
    this.root.dispatchEvent(new CustomEvent('paginate', { detail: meta }));
    this.onChange?.(meta);
  }
  setLoading(isLoading) {
    this.root.classList.toggle('is-loading', Boolean(isLoading));
  }
  destroy() {
    this.list.removeEventListener('click', this.handleNumberClick);
  }
}
function clamp(value, min, max) {
  return Math.min(max, Math.max(min, value));
}

function buildTokens(current, total, siblings) {
  const first = 1;
  const last = total;
  const left = Math.max(current - siblings, first);
  const right = Math.min(current + siblings, last);

  const out = [first];

  if (left > first + 1) out.push('...');
  for (let page = left; page <= right; page++) {
    if (page !== first && page !== last) out.push(page);
  }
  if (right < last - 1) out.push('...');
  if (last !== first) out.push(last);

  if (!out.includes(current) && current !== first && current !== last) {
    const index = out.findIndex((p) => p !== '...' && p > current);
    if (index === -1) out.push(current);
    else out.splice(index, 0, current);
  }

  return out;
}
