export function initFilterTabs() {
  const items = document.querySelectorAll('.filter__item');
  items.forEach((item) => {
    const top = item.querySelector('.filter__top');
    item.classList.add('active');

    top.addEventListener('click', () => {
      item.classList.toggle('active');
    });
  });
}
