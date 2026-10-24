export function initDrawerPanels() {
  const drawer = document.getElementById('mobile-drawer');
  if (!drawer) return;

  const layer = document.getElementById('drawer-panels');
  const back = drawer.querySelector('[data-back]');
  const close = drawer.querySelector('[data-close]');
  const title = drawer.querySelector('.mobile-drawer__title');
  const BASE = title ? (title.textContent || '').trim() : '';

  const panels = new Map(
    [...layer.querySelectorAll('[data-panel]')].map((p) => [p.dataset.key, p])
  );
  const stack = [];

  panels.forEach((el) => el.classList.add('is-next'));

  const applyState = (active, from = null, dir = 'forward') => {
    panels.forEach((el, key) => {
      el.classList.remove('is-active', 'is-prev', 'is-next');
      if (key === active) el.classList.add('is-active');
      else if (key === from)
        el.classList.add(dir === 'back' ? 'is-next' : 'is-prev');
      else el.classList.add('is-next');
    });
  };

  const openLayer = () => {
    layer.classList.add('is-open');
    drawer.classList.add('panels-open');
  };
  const closeLayer = () => {
    layer.classList.remove('is-open');
    drawer.classList.remove('panels-open');
    stack.length = 0;
    if (title) title.textContent = BASE;
  };

  const openRoot = (key) => {
    if (!panels.has(key)) return;
    stack.length = 0;
    stack.push(key);
    applyState(key);
    openLayer();
  };
  const openNext = (key) => {
    if (!panels.has(key)) return;
    const from = stack[stack.length - 1];
    stack.push(key);
    applyState(key, from, 'forward');
    openLayer();
  };
  const goBack = () => {
    if (stack.length <= 1) return closeLayer();
    const from = stack.pop();
    applyState(stack[stack.length - 1], from, 'back');
  };

  back?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    goBack();
  });
  close?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    closeLayer();
  });

  drawer.addEventListener('click', (e) => {
    if (e.target.closest('[data-back], [data-close]')) {
      return;
    }

    const op = e.target.closest('[data-open-panel]');
    if (op) {
      e.preventDefault();
      openRoot(op.dataset.openPanel);
    }
  });

  layer.addEventListener('click', (e) => {
    if (e.target.closest('[data-back], [data-close]')) return;

    const to = e.target.closest('[data-go]');
    if (to) {
      e.preventDefault();
      e.stopPropagation();
      openNext(to.dataset.go);
    }
  });
}
