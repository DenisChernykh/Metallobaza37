import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/parallax';
import 'swiper/css/thumbs';
import 'nouislider/dist/nouislider.css';
import {
	initMainBlockSlider,
	initProductSlider,
	initPromotionSlider,
} from './modules/swiper';
import { initFilterTabs } from './modules/filter-tabs';
import { initToggleViewMode } from './modules/toggle-view-mode';
import { initSelect } from './modules/select';
import { initMobileFilter } from './modules/mobile-filter';
import { initToggleFavorite } from './modules/toggle-favorite';
import { initFilter } from './modules/filter';
import { initPagination } from './modules/catalog-pagination';
import { initSort } from './modules/catalog-sort';
import { initCheckProductFavorite } from './modules/check-product-favorite';
import { initMask } from './modules/mask';
import { initDropdown } from './lib/presets/initDropdown';
import { initTabs } from './lib/presets/initTabs';
import '../../scss/style.scss';
import { initMobileMenu } from './modules/mobile-menu';
import { initDrawerPanels } from './modules/drawer-panel';
import { initHeaderSearch } from './modules/header-search';
import { initModals } from './lib/presets/InitModal';
import { CopyToClipboard } from './lib/copy/CopyToClipboard';
import { initVictoryMap } from './modules/victorymap';
import { initNoUiSlider } from './modules/noUiSlider';
import { initChangeVariant } from './modules/change-variant-button/change-variant';
import { initVgSelect } from './lib/select-lib/vgselect';
import.meta.glob('../../assets/img/icons/*.svg', { eager: true });
document.addEventListener('DOMContentLoaded', () => {
	initPromotionSlider();
	initProductSlider();
	initMainBlockSlider();
	initFilterTabs();
	initToggleViewMode();
	initSelect();
	initMobileFilter();
	initToggleFavorite();
	initMask();
	initMobileMenu();
	initDrawerPanels();
	initHeaderSearch();
	initModals();
	initNoUiSlider();
	new CopyToClipboard();
	document.querySelectorAll('[data-disclosure]').forEach(initDropdown);
	document.querySelectorAll('[data-tabs]').forEach(initTabs);
	document.addEventListener('vg-mailer:success:fastbuyproduct', () => {
		window.location.href = '/thank-you-order';
	});
	document.addEventListener('vg-mailer:success:checkoutproducts', () => {
		window.location.href = '/thank-you-order';
	});
	document.addEventListener('vg-mailer:success', () => {
		window.location.href = '/thank-you-feedback';
	});
	initVictoryMap();
	initCheckProductFavorite();
	initPagination();
	initSort();
	initVgSelect();
	// initChangeVariant();
	initFilter();
});
