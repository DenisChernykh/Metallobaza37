<div class="container">
	<section class="category">
		<div class="catalog-grid" aria-label="Категории каталога">

			<div class="catalog-grid__top">
				<h2>Каталог товаров</h2>

			</div>
			<ul class="catalog-grid__list">
				<?php get_template_part('template-parts/catalog-card') ?>
				<?php get_template_part('template-parts/catalog-card') ?>
				<?php get_template_part('template-parts/catalog-card') ?>

			</ul>
			<button class="btn">Показать еще</button>

			</в>
			<div class="category__top">
				<h4>Фильтры товаров</h4>
				<div class="category__control">
					<div class="category__toggle">
						<div data-view='list' class="category__list active"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 12H21M13 18H21M13 6H21M3 12H4M3 18H4M3 6H4M8 12H9M8 18H9M8 6H9" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</div>
						<div data-view='grid' class="category__grid"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M7 1H2C1.44772 1 1 1.44772 1 2V7C1 7.55228 1.44772 8 2 8H7C7.55228 8 8 7.55228 8 7V2C8 1.44772 7.55228 1 7 1Z" stroke="#909092" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M18 1H13C12.4477 1 12 1.44772 12 2V7C12 7.55228 12.4477 8 13 8H18C18.5523 8 19 7.55228 19 7V2C19 1.44772 18.5523 1 18 1Z" stroke="#909092" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M18 12H13C12.4477 12 12 12.4477 12 13V18C12 18.5523 12.4477 19 13 19H18C18.5523 19 19 18.5523 19 18V13C19 12.4477 18.5523 12 18 12Z" stroke="#909092" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M7 12H2C1.44772 12 1 12.4477 1 13V18C1 18.5523 1.44772 19 2 19H7C7.55228 19 8 18.5523 8 18V13C8 12.4477 7.55228 12 7 12Z" stroke="#909092" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</div>
					</div>
					<div class="select__block">
						<select name="sort" id="sort">
							<option id="select_id-1" value="sort=p.price&amp;order=ASC&amp;sort_id=1">Цена по возрастанию
							</option>
							<option id="select_id-2" value="sort=p.price&amp;order=DESC&amp;sort_id=2">Цена по убыванию</option>
							<option id="select_id-3" value="sort=p.discount&amp;order=ASC&amp;sort_id=3">По размеру скидки</option>
						</select>
						<div class="select__list">
							<div class="select__title ">
								<span id="current_sort">Цена по возрастанию</span>
								<svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1.20068 6.66732L6.53402 1.33398L11.8674 6.66732" stroke="#4F4D56" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</div>
							<ul class="select__options ">
								<li class="select__option" data-option-value="sort=p.price&amp;order=ASC&amp;sort_id=1">Цена по возрастанию</li>
								<li class="select__option" data-option-value="sort=p.price&amp;order=DESC&amp;sort_id=2">Цена по убыванию</li>
								<li class="select__option" data-option-value="sort=p.discount&amp;order=ASC&amp;sort_id=3">По размеру скидки</li>
							</ul>
						</div>
					</div>
					<div class="mobile-filter-toggle">
						<span>Фильтры</span>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1 3L9 3M1 9H9M9 9V11M9 9V7M1 15H5M9 15L17 15M13 9H17M13 3L17 3M13 3V5M13 3V1M5.5 17V13" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
						</svg>

					</div>
				</div>
			</div>
			<div class="category__wrapper">
				<?php get_template_part('template-parts/product-page/filter') ?>
				<?php get_template_part('template-parts/product-page/product-list') ?>
				<?php get_template_part('template-parts/product-page/product-grid') ?>

			</div>
	</section>
</div>