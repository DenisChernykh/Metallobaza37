export function initMobileMenu() {
  const bpMobile = 1200;

  const burger = document.querySelector('.burger--menu');
  const drawer = document.getElementById('mobile-drawer');

  const topbarContainer = document.querySelector('.topbar .container');
  const topbarNav = document.querySelector('.topbar__nav');

  const drawerMenuSlot = document.getElementById('drawer-menu-slot');

  const navPlaceholder = document.createElement('div');

  navPlaceholder.setAttribute('data-placeholder', 'nav');

  if (topbarNav && topbarNav.parentNode) {
    topbarNav.parentNode.insertBefore(navPlaceholder, topbarNav);
  }

  function setHeaderHeightVar() {
    const topbar = document.querySelector('.topbar');
    const mainbar = document.querySelector('.mainbar');
    const h = (topbar?.offsetHeight || 0) + (mainbar?.offsetHeight || 0) + 'px';
    document.documentElement.style.setProperty('--header-h', h);
  }

  function isMobile() {
    return window.innerWidth <= bpMobile;
  }

  function moveToDrawer() {
    if (!drawer) return;

    if (isMobile()) {
      if (
        topbarNav &&
        drawerMenuSlot &&
        topbarNav.parentNode !== drawerMenuSlot
      ) {
        drawerMenuSlot.appendChild(topbarNav);
      }
    }
  }

  function moveBackToTopbar() {
    if (
      navPlaceholder?.parentNode &&
      topbarNav &&
      topbarNav.parentNode !== navPlaceholder.parentNode
    ) {
      navPlaceholder.parentNode.insertBefore(topbarNav, navPlaceholder);
    }
  }
  function lockScroll() {
    document.documentElement.classList.add('is-locked');
  }
  function unlockScroll() {
    document.documentElement.classList.remove('is-locked');
  }
  function openDrawer() {
    setHeaderHeightVar();
    moveToDrawer();
    drawer?.setAttribute('data-state', 'open');
    drawer?.classList.add('is-open');

    lockScroll();
  }

  function closeDrawer() {
    drawer?.classList.remove('is-open');

    unlockScroll();
  }

  burger?.addEventListener('click', () => {
    const isOpen = drawer?.classList.contains('is-open');
    if (isOpen) closeDrawer();
    else openDrawer();
  });

  window.addEventListener(
    'keydown',
    (e) => e.key === 'Escape' && closeDrawer()
  );

  window.addEventListener('resize', () => {
    if (isMobile()) {
      setHeaderHeightVar();
    } else {
      closeDrawer();
      moveBackToTopbar();
    }
  });

  if (isMobile()) {
    setHeaderHeightVar();
  }
}
