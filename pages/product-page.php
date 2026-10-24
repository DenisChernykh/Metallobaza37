<?php

/**
 * Template Name: Товар
 */
get_header() ?>
<div class="container">
	<div class="product-top">
		<div class="product-top__breadcrumbs">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
		</div>
		<div class="product-top__info">
			<div class="product-top__sku">Артикул: 001011</div>
			<div class="product-top__share"><svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M8.00065 0.666016V11.4993M8.00065 0.666016L11.334 3.99935M8.00065 0.666016L4.66732 3.99935M1.33398 8.99935V15.666C1.33398 16.108 1.50958 16.532 1.82214 16.8445C2.1347 17.1571 2.55862 17.3327 3.00065 17.3327H13.0007C13.4427 17.3327 13.8666 17.1571 14.1792 16.8445C14.4917 16.532 14.6673 16.108 14.6673 15.666V8.99935" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
				Поделиться</div>
		</div>
	</div>
	<div class="product__row ">
		<div class="product__left product-slider">
			<div class="product-slider__layout">
				<div class="product-slider-thumb swiper mySwiper2">
					<div class="product-slider-thumb swiper-wrapper ">
						<div class="product-slider-thumb swiper-slide"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура"></div>
						<div class="product-slider-thumb swiper-slide"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/2.jpg" alt="Изоборажение Арматура"></div>
						<div class="product-slider-thumb swiper-slide"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура"></div>
						<div class="product-slider-thumb swiper-slide"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура"></div>
						<div class="product-slider-thumb swiper-slide"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура"></div>
					</div>
				</div>
				<div class=" product-slider swiper mySwiper">
					<div class="product-slider__wrapper swiper-wrapper">
						<div class="product-slider__slide swiper-slide">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура">
						</div>
						<div class="product-slider__slide swiper-slide">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/2.jpg" alt="Изоборажение Арматура">
						</div>
						<div class="product-slider__slide swiper-slide">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура">
						</div>
						<div class="product-slider__slide swiper-slide">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура">
						</div>
						<div class="product-slider__slide swiper-slide">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.jpg" alt="Изоборажение Арматура">
						</div>
					</div>
				</div>
			</div>
			<div data-tabs class="product-tabs">

				<div class="product-tabs__list" data-triggers>
					<button data-tab class="product-tabs__title active">Описание</button>
					<button data-tab class="product-tabs__title">Характеристики</button>
					<button data-tab class="product-tabs__title">Доставка и оплата</button>
				</div>
				<div class="product-tabs__panel" data-panels>
					<div data-panel class="product-tabs__content">Арматура — ключевой элемент в строительстве и ремонте, обеспечивающий прочность и долговечность бетонных конструкций. В компании МЕТАЛЛОБАЗА Ивтент в городе Иваново вы можете приобрести арматуру А1 (катанка 6 мм) по выгодным ценам.</div>
					<div data-panel class="product-tabs__content">
						<div class="product-characteristic">
							<div class="product-characteristic__label">Артикул</div>
							<div class="product-characteristic__line"></div>
							<div class="product-characteristic__value red">001011
								<button copy-text="001011" class="product-characteristic__copy">
									<svg viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1.6875 5.90625V2.25H1.125C0.503721 2.25 0 2.75372 0 3.375V7.875C0 8.49628 0.503721 9 1.125 9H5.625C6.24628 9 6.75 8.49628 6.75 7.875V7.3125H3.09375C2.47247 7.3125 1.6875 6.52753 1.6875 5.90625ZM7.875 0H3.375C2.75372 0 2.25 0.503721 2.25 1.125V5.625C2.25 6.24628 2.75372 6.75 3.375 6.75H7.875C8.49628 6.75 9 6.24628 9 5.625V1.125C9 0.503721 8.49628 0 7.875 0Z" fill="#FF5154" />
									</svg>
								</button>
							</div>
						</div>
						<div class="product-characteristic">
							<div class="product-characteristic__label">Диаметр:</div>
							<div class="product-characteristic__line"></div>
							<div class="product-characteristic__value">6 мм
							</div>
						</div>
						<div class="product-characteristic">
							<div class="product-characteristic__label">Размер:</div>
							<div class="product-characteristic__line"></div>
							<div class="product-characteristic__value">6 мм
							</div>
						</div>
						<div class="product-characteristic">
							<div class="product-characteristic__label">Класс арматуры:</div>
							<div class="product-characteristic__line"></div>
							<div class="product-characteristic__value">А - 3
							</div>
						</div>
						<div class="product-characteristic">
							<div class="product-characteristic__label">Поверхность арматуры:</div>
							<div class="product-characteristic__line"></div>
							<div class="product-characteristic__value">Гладкая
							</div>
						</div>

					</div>
					<div data-panel class="product-tabs__content">
						<p>
							«Металлобаза Ивтент» сотрудничает как с юридическими, так и с физическими лицами. Оплата по счёту за доставку может осуществляться безналичным и наличным способами.
						</p>
						<p>Доставка металлопроката осуществляется собственным и наёмным автотранспортом.</p>
						<a href="/delivery" class="red">Посмотреть условия доставки и оплаты</a>
					</div>
				</div>





			</div>
		</div>
		<div class="product__right ">
			<div class="product__info product-info">
				<h3 class="h3 product-info__title">Арматура (гладкая) ф 06</h3>
				<div class="product-info__price-block">
					<div class="product-info__price-text">Цена за погонный метр</div>
					<div class="product-info__price ">
						<div class="current">25 678 Р <span>м.п</span></div>
						<div class="old">19,00 Р / м.п</div>
					</div>
					<div class="product-info__status"><svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13 0.875L4.75 9.125L1 5.375" stroke="#19AA55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						В наличии</div>

				</div>
				<div class="product-info__variants variants">
					<div class="variants__text">Вид арматуры:</div>
					<div class="variants__buttons">
						<a href="#" class="variants__button active">6 мм</a>
						<a href="#" class="variants__button">8 мм</a>
						<a href="#" class="variants__button">16 мм</a>
						<a href="#" class="variants__button">30 мм</a>
						<a href="#" class="variants__button">40 мм</a>
						<a href="#" class="variants__button">50 мм</a>
					</div>
				</div>
				<div class="product-info__order order">
					<div class="quantity">
						<button class="quantity__btn quantity__btn--minus">−</button>
						<input type="hidden" class="quantity__value" value="1">
						<span type="text" class="quantity__input quantity__text">1</span>
						<button class="quantity__btn quantity__btn--plus">+</button>
					</div>
					<button class="order__cart btn btn--fill">В корзину</button>
					<button class="order__btn btn	">Заказать в один клик</button>
				</div>
				<div class="product-services">
					<label class="product-services__item">
						<span class="product-services__text">
							<span class="product-services__label">Рез</span>
							<span class="product-services__price">— 5 ₽/п.м.</span>
						</span>
						<input type="checkbox" class="product-services__checkbox" />
						<span class="product-services__toggle"></span>
					</label>
				</div>


			</div>
			<?php get_template_part('template-parts/product-delivery') ?>
		</div>

	</div>
	<section class="promotions" aria-label="Категории каталога">
		<div class="container">
			<div class="catalog-grid__top">
				<h2 class="h2">Популярное в данной категории</h2>

			</div>
			<div class="slider">
				<div class="swiper">
					<div class="swiper-wrapper">
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
						<div class="swiper-slide"><?php get_template_part('template-parts/product-card') ?></div>
					</div>
				</div>
				<button class="slider-button slider-button--prev">
					<svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.072 21.0928C10.2673 21.2881 10.5839 21.2881 10.7792 21.0928C10.9744 20.8976 10.9744 20.581 10.7791 20.3857L10.072 21.0928ZM0.686036 11L0.332482 10.6464C0.238713 10.7402 0.186035 10.8674 0.186036 11C0.186037 11.1326 0.238717 11.2598 0.332487 11.3536L0.686036 11ZM10.6405 1.75265C10.8358 1.55739 10.8358 1.24081 10.6405 1.04554C10.4452 0.850281 10.1286 0.850281 9.93339 1.04554L10.6405 1.75265ZM23.3135 11.5L23.8135 11.5V10.5L23.3135 10.5V11.5ZM10.4256 20.7393L10.7791 20.3857L1.03958 10.6464L0.686036 11L0.332487 11.3536L10.072 21.0928L10.4256 20.7393ZM0.686036 11L1.03959 11.3536L10.6405 1.75265L10.2869 1.3991L9.93339 1.04554L0.332482 10.6464L0.686036 11ZM0.686036 11L0.686036 11.5L23.3135 11.5L23.3135 11L23.3135 10.5L0.686036 10.5L0.686036 11Z" fill="#FF5154" />
					</svg>
				</button>
				<button class="slider-button slider-button--next">
					<svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.072 21.0928C10.2673 21.2881 10.5839 21.2881 10.7792 21.0928C10.9744 20.8976 10.9744 20.581 10.7791 20.3857L10.072 21.0928ZM0.686036 11L0.332482 10.6464C0.238713 10.7402 0.186035 10.8674 0.186036 11C0.186037 11.1326 0.238717 11.2598 0.332487 11.3536L0.686036 11ZM10.6405 1.75265C10.8358 1.55739 10.8358 1.24081 10.6405 1.04554C10.4452 0.850281 10.1286 0.850281 9.93339 1.04554L10.6405 1.75265ZM23.3135 11.5L23.8135 11.5V10.5L23.3135 10.5V11.5ZM10.4256 20.7393L10.7791 20.3857L1.03958 10.6464L0.686036 11L0.332487 11.3536L10.072 21.0928L10.4256 20.7393ZM0.686036 11L1.03959 11.3536L10.6405 1.75265L10.2869 1.3991L9.93339 1.04554L0.332482 10.6464L0.686036 11ZM0.686036 11L0.686036 11.5L23.3135 11.5L23.3135 11L23.3135 10.5L0.686036 10.5L0.686036 11Z" fill="#FF5154" />
					</svg>
				</button>
			</div>


			<div class="swiper-pagination"></div>





		</div>
	</section>

</div>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<div class="map">
	<a href="<?php opt_email_href('address') ?>" target="_blank" rel="nofollow">
	</a>
</div>




</section>
<?php get_footer() ?>