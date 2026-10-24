export function initMobileFilter() {
  const button = document.querySelector('.mobile-filter-toggle');
  const filter = document.querySelector('.filter');
  const body = document.querySelector('body');
  const grabber = document.querySelector('.filter__grabber');
  if (!grabber) return;

  let startY = 0;
  let currentY = 0;
  let isDragging = false;
  grabber.addEventListener('touchstart', (e) => {
    isDragging = true;
    startY = e.touches[0].clientY;
    currentY = startY;
  });

  grabber.addEventListener('touchmove', (e) => {
    if (!isDragging) return;
    currentY = e.touches[0].clientY;

    const deltaY = currentY - startY;

    if (deltaY > 0) {
      filter.style.transform = `translateY(${deltaY}px)`;
    }
  });

  grabber.addEventListener('touchend', () => {
    if (!isDragging) return;
    isDragging = false;

    const deltaY = Math.max(0, currentY - startY);
    if (deltaY > 50) {
      closeFilter();
      filter.style.transform = '';
    } else {
      filter.style.transform = '';
    }

    startY = 0;
    currentY = 0;
  });
  button.addEventListener('click', openFilter);
  if (!filter || !grabber) return;
  function openFilter() {
    filter.classList.add('open');
    body.style.overflow = 'hidden';
  }
  function closeFilter() {
    filter.classList.remove('open');
    document.body.style.overflow = '';
    filter.style.transform = '';
  }
}
