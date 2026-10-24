<?php
require_once __DIR__ . "/../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Helpers\CategoryHelper;



use VG_Catalog\Domain\Services\CategoryService;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetListCategoriesAction;
use VG_Catalog\Domain\UseCases\Categories\Dto\FilterCategoriesDTO;

$cartService  = new CartService();
$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartData = $cartService->getList($paginationDTO);

$cartTotals = array_reduce($cartData, function ($acc, $row) {
	$qty   = (int)($row['quantity'] ?? 1);
	$price = (float)($row['product']['price'] ?? 0);
	$sale  = (float)($row['product']['sale_price'] ?? 0);
	$final = ($sale > 0 && $sale < $price) ? $sale : $price;

	$acc['qty']   += $qty;
	$acc['base']  += $price * $qty;
	$acc['final'] += $final * $qty;
	return $acc;
}, ['qty' => 0, 'base' => 0.0, 'final' => 0.0]);

$cartTotals['discount'] = max(0, $cartTotals['base'] - $cartTotals['final']);
$normalizedCart = [
	'id'      => 'router',
	'version' => 0,
	'items'   => array_map(function ($row) {
		$qty   = (int)($row['quantity'] ?? 1);
		$p     = (float)($row['product']['price'] ?? 0);
		$s     = (float)($row['product']['sale_price'] ?? 0);
		$final = ($s > 0 && $s < $p) ? $s : $p;
		$unit = $row['product']['unit']->name ?? 'м.п';
		$price = $row['product']['price'] ?? 0;
		$sale_price = $row['product']['sale_price'] ?? 0;



		return [
			'id'        => (string)($row['id'] ?? ''),
			'productId' => (string)($row['product_id'] ?? 0),
			'name'      => (string)($row['product']['title'] ?? ''),
			'sku'       => (string)($row['product']['sku'] ?? ''),
			'quantity'  => $qty,
			'price'     => $final,
			'isHit'     => !empty($row['product']['is_hit']) && $row['product']['is_hit'] === '1',
			'hasSale' =>  $s > 0 && $s < $p,
			'unit' => '/ ' . $unit,
			'price' => $price,
			'salePrice' => $sale_price,
			'priceFormatted' => number_format($final, 0, '', ' ') . ' ₽',
			'originalPriceFormatted' => number_format($p, 0, '', ' ') . ' ₽'
		];
	}, $cartData),
	'totals'  => [
		'items_qty' => $cartTotals['qty'],
		'sum_without_discount' => $cartTotals['base'],
		'sum_with_discount'    => $cartTotals['final'],
		'discount'             => $cartTotals['discount'],
		'sum_without_discount_formatted' => number_format($cartTotals['base'], 0, '', ' ') . ' ₽',
		'sum_with_discount_formatted'    => number_format($cartTotals['final'], 0, '', ' ') . ' ₽',
		'discount_formatted'
	],
];
$normalizedCart = metal_get_normalized_cart();

$categoryService = new CategoryService();
$categoryList = $categoryService->getList(new FilterCategoriesDTO(['parent_id' => 0]), new GetListCategoriesAction);
$_SESSION['category_list'] = $categoryList;

$price_list_id = carbon_get_theme_option('price_list_google_url');
$price_list_url = $price_list_id ? wp_get_attachment_url($price_list_id) : '';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>

<body <?php body_class() ?>>

	<div class="site-overlay"></div>
	<header class="header">



		<div class="topbar" role="region" aria-label="Служебная панель">

			<div class="container">
				<div class="search-mobile">
					<?php the_icon('search') ?>
				</div>

				<?php wp_nav_menu([
					'theme_location' => 'topbar',
					'container'      => 'nav',
					'container_class' => 'topbar__nav',
					'container_aria_label' => 'Верхнее меню',
					'menu_class'     => 'menu menu--top',
					'li_class' => 'menu__item underLink',
					'a_class' => 'menu__link'

				]);
				?>
				<address class="contacts" aria-label="Контакты">
					<ul class="contacts__list">
						<li class="contacts__item contacts__item--email">
							<span class="contacts__icon contacts__icon--email" aria-hidden="true">
								<svg class="icon">
									<use href="#i-mail"></use>
								</svg>
							</span>
							<?php $email = my_opt('email'); ?>
							<a class="contacts__link underLink" href="mailto:<?php echo esc_attr($email) ?>"><?php echo esc_html($email)  ?></a>
						</li>
						<li class="contacts__item contacts__item--location">
							<span class="contacts__icon contacts__icon--location" aria-hidden="true">
								<svg class="icon">
									<use href="#i-loc"></use>
								</svg>
							</span>
							<a
								class="contacts__link underLink"
								target="_blank" rel="nofollow"
								href="<?php opt_map_href('address') ?>"><?php echo carbon_get_theme_option('address'); ?></a>
						</li>

						<li class="contacts__item contacts__item--phones" data-disclosure data-state="closed">
							<div class="contacts__item contacts__item--phones" data-toggle>
								<span class="contacts__icon contacts__icon--phone" aria-hidden="true">
									<svg class="icon">
										<use href="#i-phone"></use>
									</svg>
								</span>
								<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>" class="contacts__link contacts__link--phone"><?php echo the_opt('phone_1'); ?></a>
								<button
									class="contacts__toggle"
									type="button">
									<?php the_icon('arrow-down-fill', 'icon--fill') ?>
								</button>
							</div>
							<ul data-panel id="contacts-submenu" class="contacts__sublist" aria-hidden="true">

								<li class="contacts__subitem">
									<a class="contacts__sublink red-color underLink" href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>"><?php echo the_opt('phone_1'); ?></a>
									<a class="contacts__sublink red-color underLink" href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_2'))) ?>"><?php echo the_opt('phone_2'); ?></a>
								</li>

								<li class="contacts__subitem">
									<div class="contacts__label">Электронная почта</div>
									<a class="contacts__sublink underLink" href="mailto:<?php echo esc_attr(my_opt('email')) ?>"><?php echo carbon_get_theme_option('email'); ?></a>
								</li>
								<li class="contacts__subitem">
									<div class="contacts__label">Адрес</div>
									<a
										target="_blank" rel="nofollow"
										class="contacts__sublink underLink"
										href="<?php opt_map_href('address') ?>"><?php echo carbon_get_theme_option('address'); ?></a>
								</li>
								<li class="contacts__subitem">
									<div class="contacts__label">Режим работы</div>
									<div class="contacts__sublink contacts__text--time"><?php the_opt_br('work_time'); ?></div>
								</li>
							</ul>
						</li>
					</ul>
				</address>
			</div>

		</div>
		<div class="mainbar">
			<div class="container">
				<div class="burger burger--menu">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="На главную">
					<img class="logo__img" src="<?php echo esc_url(wp_get_attachment_image_url(carbon_get_theme_option('site_logo_header'), 'full')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
				</a>


				<div class="catalog" data-disclosure data-state="closed" data-overlay="true">
					<button data-toggle type="button" aria-expanded="false" aria-controls="catalog-panel" type="button" class="catalog__toggle btn btn--black">
						<span class="catalog__toggle-text">Каталог</span>
						<div class="burger">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</button>
					<div id="catalog-panel" class="catalog__panel" data-panel aria-hidden="true">
						<ul class="catalog__list">
							<?php foreach ($categoryList as $cat): ?>
								<?php if (!empty($cat['children'])): ?>
									<li data-state="closed" data-disclosure class="catalog__item catalog__item--has-children">
										<div data-toggle class="catalog__item-row">
											<span class="catalog__thumb">
												<img src="<?= $cat['image'] ?>"
													alt="Категория: <?= $cat['name'] ?>">
											</span>
											<button class="catalog__item-toggle" type="button" aria-expanded="false" aria-controls="catalog-sub-armatura">
												<span class="catalog__item-toggle-text"><?= $cat['name'] ?></span>
												<span aria-hidden="true" class="catalog__arrow">
													<?php the_icon('arrow-right-fill')
													?>
												</span>
											</button>
										</div>
										<div id='catalog-sub-armatura' data-panel class="catalog__subpanel" aria-hidden="true">
											<div class="catalog__subheader"><?= $cat['name'] ?></div>
											<ul class="catalog__sublist">
												<?php foreach ($cat['children'] as $child): ?>
													<li class="catalog__subitem"><a class="underLink" href="<?= $child['link'] ?>" class="catalog__sublink"><?= $child['name'] ?></a></li>
												<?php endforeach; ?>
											</ul>
										</div>
									</li>
								<?php else: ?>
									<li class="catalog__item">
										<div class="catalog__item-row">
											<a class="catalog__link" href="<?= $cat['link'] ?>">
												<span class="catalog__thumb">
													<img src="<?= $cat['image'] ?>" alt="Категория: <?= $cat['name'] ?>">
												</span>
												<div class="catalog__text"><?= $cat['name'] ?></div>
											</a>
										</div>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php
				$google_url = (string) carbon_get_theme_option('price_list_google_url');
				$pdf_url    = metallobaza_google_sheets_to_pdf_export_url($google_url);
			

				?>
				<?php get_search_form() ?>
				<?php if (!empty($price_list_url )): ?>
					<a
						href="<?php echo esc_url($price_list_url ); ?>"
						class="price-list-link"
						target="_blank"
						rel="noopener">
						Скачать прайс-лист
					</a>
				<?php endif; ?>


				<button data-modal-open="#modal-contact" class="btn btn--call">Заказать звонок</button>
				<div class="mainbar-icons">
					<a href="/favorite" class="favorite-icon	">
						<?php the_icon('favorite') ?>
					</a>
					<?php metal_print_cart_state();
					ob_start(); ?>
					<div class="mainbar-icons">
						<a data-wp-interactive="cart" href="/cart" class="cart-icon"><span data-wp-bind--hidden="!state.cart.totals.items_qty" data-wp-text="state.cart.totals.items_qty" class="cart-icon__quantity">
							</span>
							<?php the_icon('cart') ?>
						</a>
					</div>
					<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>
				</div>
			</div>
		</div>

		<nav id="mobile-drawer" class="mobile-drawer" aria-label="Мобильное меню">
			<div class="mobile-drawer__inner">
				<div class="mobile-drawer__header" data-open-panel="tax:product_category:0"
					data-title="Каталог">
					<button class="menu__link h4 " type="button">Каталог <?php the_icon('arrow-right-fill', 'icon--fill') ?></button>
					<button type="button" class="mobile-drawer__back" data-back aria-label="Назад"><?php the_icon('arrow-long') ?> Назад</button>
				</div>
				<div class="h4 mobile-drawer__title" data-title>Компания</div>
				<div id="drawer-menu-slot"></div>
				<div class="mobile-drawer__contacts">
					<div class="mobile-drawer__top">
						<div class="mobile-drawer__item">
							<div class="mobile-drawer__title--contacts">Адрес</div>
							<a target="_blank" rel="nofollow" href="<?php opt_email_href('address') ?>">
								<div class="mobile-drawer__text "><?php echo carbon_get_theme_option('address'); ?></div>
							</a>


						</div>
						<div class="mobile-drawer__item">
							<div class="mobile-drawer__title--contacts">Режим работы</div>
							<div class="mobile-drawer__text"><?php echo carbon_get_theme_option('work_time') ?></div>

						</div>
					</div>
					<div class="mobile-drawer__bottom">
						<div class="mobile-drawer__item mobile-drawer__item--phone">
							<div class="mobile-drawer__title--contacts">Телефон</div>
							<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>" class="mobile-drawer__text mobile-drawer__text--phone"><?php echo carbon_get_theme_option('phone_1'); ?></a><br>
							<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_2'))) ?>" class="mobile-drawer__text mobile-drawer__text--phone"><?php echo carbon_get_theme_option('phone_2'); ?></a>
							<a data-modal-open="#modal-contact" href="#" class="mobile-drawer__text mobile-drawer__text--feedback">Заказать звонок</a>

						</div>
						<div class="mobile-drawer__item">
							<div class="mobile-drawer__title--contacts">Электронная почта</div>
							<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="mobile-drawer__text"><?php echo carbon_get_theme_option('email'); ?></a>
						</div>
					</div>
				</div>

                <?php if(!empty($price_list_url)):?>
                    <a class="mobile-drawer__download"
                       href="<?= esc_url($price_list_url )?>"
                       rel="noopener">
                        Скачать прайс-лист
                    </a>
                <?php endif;?>

				<div id="drawer-panels" class="mobile-drawer__panels">
					<section class="panel"
						data-panel
						data-key="tax:product_category:0"
						data-title="Каталог"
						aria-hidden="true">
						<ul class="panel__list">

							<?php foreach ($categoryList as $cat): ?>
									<?php if (!empty($cat['children'])): ?>
										<li class="panel__item">
											<button type="button" class="panel__link"
												data-go="tax:product_category:<?= (int)$cat['id'] ?>"
												data-title="<?= esc_attr($cat['name']) ?>">
												<?= esc_html($cat['name']) ?> <span aria-hidden="true"><?php the_icon('arrow-right-fill') ?></span>
											</button>
										</li>
								<?php else: ?>
									<li class="panel__item">
										<a class="panel__link" href="<?= $cat['link'] ?>"><?= $cat['name'] ?></a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</section>

					<?php foreach ($categoryList as $cat): ?>
							<?php if (!empty($cat['children'])): ?>
								<section class="panel"
									data-panel
									data-key="tax:product_category:<?= (int)$cat['id'] ?>"
									data-title="<?= esc_attr($cat['name']) ?>">
									<ul class="panel__list">
									<?php foreach ($cat['children'] as $child): ?>
										<li class="panel__item"><a class="panel__link" href="<?= $child['link'] ?>"><?= $child['name'] ?></a></li>
									<?php endforeach; ?>
								</ul>
							</section>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</nav>

	</header>
