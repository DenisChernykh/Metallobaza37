import Swiper from 'swiper';
import {
  Navigation,
  Pagination,
  Keyboard,
  Thumbs,
  FreeMode,
} from 'swiper/modules';
function getRem() {
  return parseFloat(getComputedStyle(document.documentElement).fontSize);
}
export function initPromotionSlider() {
  const container = document.querySelector('.promotions .swiper');
  const swiper = new Swiper(container, {
    modules: [Navigation, Pagination, Keyboard],
    // Observe DOM mutations to keep Swiper in sync with IAPI-rendered slides
    observer: true,
    observeParents: true,
    observeSlideChildren: true,
    speed: 500,
    spaceBetween: 40 * getRem(),
    watchOverflow: true,
    slidesPerView: 4,
    breakpoints: {
      0: { slidesPerView: 1.7, spaceBetween: 8 * getRem() },
      //   480: { slidesPerView: 1.9 },
      768: { slidesPerView: 4, spaceBetween: 20 * getRem() },
      1200: { slidesPerView: 4, spaceBetween: 40 * getRem() },
    },
    a11y: { enabled: true, messageOnFocus: true },
    keyboard: { enabled: true },

    pagination: {
      el: '.promotions .swiper-pagination',
      clickable: true,
      type: 'progressbar',
    },
    navigation: {
      prevEl: ' .slider-button--prev',
      nextEl: '.slider-button--next',
    },
  });
}

export function initProductSlider() {
  const container = document.querySelector('.mySwiper');
  const container2 = document.querySelector('.mySwiper2');
  const swiper = new Swiper(container, {
    modules: [Thumbs],
    watchSlidesProgress: true,
    direction: 'vertical',
    slidesPerView: 'auto',
    spaceBetween: 20 * getRem(),
    watchSlidesProgress: true,
    breakpoints: {
      0: { direction: 'horizontal', spaceBetween: 12 * getRem() },
      768: { direction: 'vertical' },
    },
  });
  const swiper2 = new Swiper(container2, {
    modules: [Thumbs],
    autoHeight: true,
    thumbs: {
      swiper: swiper,
    },
  });
}

export function initMainBlockSlider() {
  const container = document.querySelector('.intro__slider');
  const swiper = new Swiper(container, {
    modules: [Navigation, Pagination],
    slidesPerView: 1,
    resistanceRatio: 0,
    pagination: {
      el: '.controls-intro__pagination',
      clickable: true,
      bulletClass: 'slider-pagination__item',
      bulletActiveClass: 'active',
      renderBullet: (index, className) => `
      <button type="button"
              class="${className}"
              aria-label="Слайд ${index + 1}">
        <span class="slider-pagination__center"></span>
      </button>
    `,
    },
    navigation: {
      prevEl: '.intro .slider-button--prev',
      nextEl: '.intro .slider-button--next',
    },
  });
}
