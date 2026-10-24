<?php get_header(); ?>

<?php

require_once __DIR__ . "/../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\ProductService;
use VG_Catalog\Domain\UseCases\Product\Actions\GetProductByIdAction;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Helpers\CategoryHelper;
use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;

$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartService  = new CartService();
$cartData = $cartService->getList($paginationDTO);


$current_url = (is_ssl() ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];



$productService  = new ProductService();
$productId = get_the_ID();
$serverQuantity = 0;

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
$productProperties = get_product_properties($productId);

$productData = $productService->getById(
	new ProductIdDTO(['product_id' => $productId]),
	new GetProductByIdAction()
);
$optionsGrouptForNormalizedCart = [];
foreach ($cartData as $row) {
	if ((string)($row['product_id'] ?? '') === (string)$productId) {
		$serverQuantity = (int)($row['quantity'] ?? 0);
		break;
	}
}


$price = $productData['price'] ?? 0;
$sale_price = $productData['sale_price'] ?? 0;
$has_sale = $sale_price > 0 && $sale_price < $price;
$unit = $productData['unit']->name ?? 'м.п';
$description = $productData['description'] ?? '';
$status = $productData['status'] ?? 'in_stock';

$rezPrice = $productData['price_cut'] ?? '5';

$optionGroups = $productData['options'] ?? [];
$mainImage = $productData['main_image'] ?? '';
$gallery = $productData['gallery'] ?? '';

$images = [];
//Добавляем основное изображение
if (!empty($mainImage)) {
	$images[] = $mainImage;
}
//Добавляем доп.изображения
foreach ($gallery as $img) {
	$images[] = $img;
}
//Заглушка
if (empty($images)) {
	$images[] = '/wp-content/themes/metallobaza37/assets/img/no_image.png';
}

$helper = new CategoryHelper();
$categories = $productData['categories'] ?? [];
$categoryId = null;

// Берем первую категорию товара
if (!empty($categories)) {
	$firstCategory = reset($categories);
	$categoryId = $firstCategory->term_id ?? $firstCategory->term_taxonomy_id ?? null;
}
//$categoryId = 6;
$limit = 6;
$excludeIds = [$productId];
$random = true;
$popularCategoryProducts = $helper->getProductsByCategoryId($categoryId, true, $limit, $excludeIds, $random);
function pick($src, $key, $default = null)
{
	if (is_array($src)) {
		return array_key_exists($key, $src) ? $src[$key] : $default;
	}
	if (is_object($src)) {
		if (isset($src->$key)) return $src->$key;
		if (method_exists($src, 'getAttribute')) {
			$v = $src->getAttribute($key);
			if ($v !== null) return $v;
		}
	}
	return $default;
}
function normalizeSelectedOptions(array $row): array
{
	$productOptionsGroups = (array)($row['product']['options'] ?? []);
	$groupsIndex = [];
	foreach ($productOptionsGroups as $g) {
		$groupsIndex[(int)($g['post_id'] ?? 0)] = (string)($g['title'] ?? '');
	}

	$selected = [];
	foreach ((array)($row['options'] ?? []) as $opt) {
		$id        = (int) pick($opt, 'id', 0);
		$postId  = (int) pick($opt, 'post_id', 0);
		$groupId   = (int) pick($opt, 'group_id', 0);
		$title     = (string) pick($opt, 'title', '');
		$priceDelta = (float) pick($opt, 'price', 0);
		$groupTitle = $groupsIndex[$groupId] ?? '';

		$selected[] = [
			'id'         => $postId,
			'groupId'    => $groupId,
			'groupTitle' => $groupTitle,
			'title'      => $title,
			'priceDelta' => $priceDelta,
			'display'    => $groupTitle ? ($groupTitle . ': ' . $title) : $title,
		];
	}
	return $selected;
}
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
		$href = $row['product']['href'] ?? '';
		$selectedOptions = normalizeSelectedOptions($row);
		$optionsSummary  = implode(', ', array_map(fn($o) => $o['display'], $selectedOptions));
		$optionsKey      = md5(json_encode(array_map(fn($o) => $o['id'], $selectedOptions)));


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
			'originalPriceFormatted' => number_format($p, 0, '', ' ') . ' ₽',
			'href' => $href,
			'options' => $selectedOptions ?? [],
			'optionsSummary'  => $optionsSummary ?? [],
			'optionsKey'      => $optionsKey ?? [],
			'rez' => (int)($row['rez'] ?? 0),
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


$optionGroupsRaw = $productData['options'] ?? [];

$normalizedProduct = [
	'optionsGroups' => array_map(function ($g) {
		$options = array_values(array_map(function ($o) {
			return [
				'id'    => (string)($o['post_id'] ?? ''),
				'title' => (string)($o['title'] ?? ''),
			];
		}, $g['options'] ?? []));

		$firstId = $options[0]['id'] ?? null;


		foreach ($options as &$opt) {
			$opt['active'] = ($firstId !== null && $opt['id'] === $firstId);
		}
		unset($opt);

		return [
			'groupId'       => (string)($g['post_id'] ?? ''),
			'title'         => (string)($g['title'] ?? ''),
			'required'      => !empty($g['required']),
			'multiple'      => !empty($g['multiple']),
			'activeVariant' => $firstId,
			'options'       => $options,
		];
	}, $optionGroupsRaw),
];


$optionGroups   = $normalizedProduct['optionsGroups'] ?? [];
$activeVariants = array_column($optionGroups, 'activeVariant');
$activeVariants = array_map('intval', $activeVariants);


?>

<?php wp_interactivity_state('cart', ['cart' => $normalizedCart, 'product' => $normalizedProduct]);
ob_start(); ?>
<div data-wp-interactive="cart" data-wp-context='{"productId": "<?= esc_attr($productId)  ?>", "quantity":<?= esc_attr($serverQuantity) ?>, "activeVariants": [<?= implode(',', $activeVariants) ?>] }' class="container">
	<input type="hidden" name="page-product-id" value="<?= $productId ?>">
	<div class="product-top">
		<div class="product-top__breadcrumbs">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
		</div>
		<div class="product-top__info">
			<?php if (!empty($productData['sku'])): ?>
				<div class="product-top__sku">Артикул: <?= $productData['sku'] ?></div>
			<?php endif; ?>
			<button data-copy data-copy-text="<?= esc_url($productData['href'])  ?>" class="product-top__share"><svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M8.00065 0.666016V11.4993M8.00065 0.666016L11.334 3.99935M8.00065 0.666016L4.66732 3.99935M1.33398 8.99935V15.666C1.33398 16.108 1.50958 16.532 1.82214 16.8445C2.1347 17.1571 2.55862 17.3327 3.00065 17.3327H13.0007C13.4427 17.3327 13.8666 17.1571 14.1792 16.8445C14.4917 16.532 14.6673 16.108 14.6673 15.666V8.99935" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
				Поделиться</button>
		</div>
	</div>

	<div class="product__row ">
		<div class="product__left product-slider">
			<div class="product-slider__layout">
				<div class="product-slider-thumb swiper mySwiper2">
					<div class="product-slider-thumb swiper-wrapper ">
						<?php foreach ($images as $index => $image): ?>
							<div class="product-slider-thumb swiper-slide"><img src="<?= $image ?>" alt="<?= $productData['title'] ?> изображение <?= $index ?>"></div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class=" product-slider swiper mySwiper">
					<div class="product-slider__wrapper swiper-wrapper">
						<?php foreach ($images as $index => $image): ?>
							<div class="product-slider__slide swiper-slide">
								<img src="<?= $image ?>" alt="<?= $productData['title'] ?> изображение <?= $index ?>">
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div data-tabs class="product-tabs">

				<div class="product-tabs__list" data-triggers>
					<?php if (!empty($description)): ?>
						<button data-tab class="product-tabs__title active">Описание</button>
					<?php endif; ?>
					<?php if (!empty($productProperties)): ?>
						<button data-tab class="product-tabs__title">Характеристики</button>
					<?php endif; ?>
					<button data-tab class="product-tabs__title">Доставка и оплата</button>
				</div>
				<div class="product-tabs__panel" data-panels>
					<?php if (!empty($description)): ?>
						<div data-panel class="product-tabs__content"><?= $description ?></div>
					<?php endif; ?>
					<?php if (!empty($productProperties)): ?>
						<div data-panel class="product-tabs__content">
							<div class="product-characteristic">
								<div class="product-characteristic__label">Артикул</div>
								<div class="product-characteristic__line"></div>
								<div class="product-characteristic__value red"><?= $productData['sku'] ?>
									<button copy-text="<?= $productData['sku'] ?>" class="product-characteristic__copy">
										<svg viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M1.6875 5.90625V2.25H1.125C0.503721 2.25 0 2.75372 0 3.375V7.875C0 8.49628 0.503721 9 1.125 9H5.625C6.24628 9 6.75 8.49628 6.75 7.875V7.3125H3.09375C2.47247 7.3125 1.6875 6.52753 1.6875 5.90625ZM7.875 0H3.375C2.75372 0 2.25 0.503721 2.25 1.125V5.625C2.25 6.24628 2.75372 6.75 3.375 6.75H7.875C8.49628 6.75 9 6.24628 9 5.625V1.125C9 0.503721 8.49628 0 7.875 0Z" fill="#FF5154" />
										</svg>
									</button>
								</div>
							</div>
							<?php foreach ($productProperties as $propName => $propValue): ?>
								<div class="product-characteristic">
									<div class="product-characteristic__label"><?= $propName ?>:</div>
									<div class="product-characteristic__line"></div>
									<div class="product-characteristic__value"><?= $propValue ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
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
				<h3 class="h3 product-info__title"><?= $productData['title'] ?></h3>
				<div class="product-info__price-block">
					<div class="product-info__price-text">Цена за погонный метр</div>
					<div class="product-info__price ">
						<div class="current"><?= number_format($has_sale ? $sale_price : $price, 0, '', ' ') ?> &#8381; <span><?= $unit ?></span></div>
						<?php if ($has_sale): ?>
							<div class="old"><?= number_format($price, 0, '', ' ') ?> &#8381; / <?= $unit ?></div>
						<?php endif; ?>
					</div>
					<?php if ($status === 'in_stock'): ?>
						<div class="product-info__status"><svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 0.875L4.75 9.125L1 5.375" stroke="#19AA55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							В наличии
						</div>
					<?php endif; ?>
				</div>




				<!-- <?php if (!empty($optionGroups)): ?>
					<div class="product-info__variants variants">
						<?php foreach ($optionGroups as $optionGroup): ?>
							<div data-group-id="<?= $optionGroup['post_id'] ?>" class="variants__text"><?= $optionGroup['title'] ?>:</div>
							<div data-variant-group class="variants__buttons">
								<?php foreach ($optionGroup['options'] as $key => $option): ?>
									<a data-variant-button data-option-id="<?= $option['post_id'] ?>" href="#" class="variants__button <?php if ($key === 0): ?>active<?php endif; ?>"><?= $option['title'] ?></a>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?> -->
				<div data-wp-bind--hidden="!state.product.optionsGroups.length" class="product-info__variants variants">
					<template data-wp-each--groups="state.product.optionsGroups" data-wp-each-key="context.groups.groupId">
						<div data-wp-text="context.groups.title" class="variants__text"></div>
						<div class="variants__buttons">
							<template data-wp-each--opts="context.groups.options">
								<button data-wp-on--click="actions.changeActiveVariant" data-wp-class--active="context.opts.active" data-wp-text="context.opts.title" class="variants__button"></button>
							</template>
						</div>

					</template>
				</div>
					<div data-wp-bind--hidden="!state.product.optionsGroups.length" data-product-id="<?= $productId ?>" data-cart-id="" class="product-info__order order">
						<div data-wp-bind--hidden="!state.quantityForSingleProduct" class="quantity">
							<button data-wp-on--click="actions.decForSingleProduct" class="quantity__btn quantity__btn--minus">−</button>
							<input data-wp-on--change="actions.setQuantityForSingleProductFromInput" data-wp-bind--value="state.quantityForSingleProduct" type="number" class="quantity__value">

							<button data-wp-on--click="actions.incForSingleProduct" class="quantity__btn quantity__btn--plus">+</button>
						</div>
						<button data-wp-on--click="actions.addCurrentProductWithOptions" data-wp-bind--hidden="state.quantityForSingleProduct" class="js-add-to-cart btn btn--fill">В корзину</button>
					<button data-modal-open="#modal-fastbuy" class="order__btn btn	">Заказать в один клик</button>
				</div>
					<div data-wp-bind--hidden="state.product.optionsGroups.length" data-product-id="<?= $productId ?>" data-cart-id="" class="product-info__order order">
						<div data-wp-bind--hidden="!state.quantityForProduct" class="quantity">
							<button data-wp-on--click="actions.decForProduct" class="quantity__btn quantity__btn--minus">−</button>
							<input data-wp-on--change="actions.setQuantityForProductFromInput" data-wp-bind--value="state.quantityForProduct" type="number" class="quantity__value">

							<button data-wp-on--click="actions.incForProduct" class="quantity__btn quantity__btn--plus">+</button>
						</div>
						<button data-wp-on--click="actions.addCurrentProduct" data-wp-bind--hidden="state.quantityForProduct" class="js-add-to-cart btn btn--fill">В корзину</button>
					<button data-modal-open="#modal-fastbuy" class="order__btn btn	">Заказать в один клик</button>
				</div>

				<div class="product-services">
					<label class="product-services__item">
						<span class="product-services__text">
							<span class="product-services__label">Рез</span>
							<span class="product-services__price">— <?=round($rezPrice)?> ₽/п.м.</span>
						</span>
						<input type="checkbox" class="product-services__checkbox" />
						<span class="product-services__toggle"></span>
					</label>
				</div>


			</div>
			<?php get_template_part('template-parts/product-delivery') ?>
		</div>

	</div>
	<?php if (!$popularCategoryProducts->isEmpty()): ?>
		<section class="promotions" aria-label="Категории каталога">
			<div class="container">
				<div class="catalog-grid__top">
					<h2 class="h2">Популярные товары в этой категории</h2>
					<?php
					$firstCategory = !empty($categories) ? reset($categories) : null;
					$categoryLink = '/';

					if ($firstCategory) {
						$link = get_term_link($firstCategory->term_id);
						if (!is_wp_error($link)) {
							$categoryLink = $link;
						}
					}

					get_template_part(
						'template-parts/buttons/text-icon-button',
						null,
						[
							'href'  => $categoryLink,
							'label' => 'Все товары',
							'class' => 'text-icon-button--red'
						]
					);
					?>
				</div>
				<div class="slider">
					<div class="swiper">
						<div class="swiper-wrapper">
							<?php foreach ($popularCategoryProducts as $product): ?>
								<div class="swiper-slide"><?php get_template_part('template-parts/product-card', null, $product) ?></div>
							<?php endforeach; ?>
						</div>
					</div>
					<button class="slider-button slider-button--prev">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
							<path d="M10.0721 21.0928C10.2674 21.2881 10.584 21.2881 10.7792 21.0928C10.9745 20.8976 10.9745 20.581 10.7792 20.3857L10.0721 21.0928ZM0.686097 11L0.332543 10.6464C0.238774 10.7402 0.186096 10.8674 0.186097 11C0.186098 11.1326 0.238778 11.2598 0.332548 11.3536L0.686097 11ZM10.6406 1.75265C10.8358 1.55739 10.8358 1.2408 10.6406 1.04554C10.4453 0.850279 10.1287 0.850279 9.93344 1.04554L10.6406 1.75265ZM23.3135 11.5L23.8135 11.5L23.8135 10.5L23.3135 10.5L23.3135 11.5ZM10.4257 20.7393L10.7792 20.3857L1.03964 10.6464L0.686097 11L0.332548 11.3536L10.0721 21.0928L10.4257 20.7393ZM0.686097 11L1.03965 11.3536L10.6406 1.75265L10.287 1.39909L9.93344 1.04554L0.332543 10.6464L0.686097 11ZM0.686097 11L0.686097 11.5L23.3135 11.5L23.3135 11L23.3135 10.5L0.686097 10.5L0.686097 11Z" fill="#909092" />
						</svg>
					</button>
					<button class="slider-button slider-button--next">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
							<path d="M13.9277 0.907158C13.7324 0.711899 13.4159 0.711903 13.2206 0.907168C13.0253 1.10243 13.0253 1.41902 13.2206 1.61428L13.9277 0.907158ZM23.3137 11L23.6673 11.3536C23.761 11.2598 23.8137 11.1326 23.8137 11C23.8137 10.8674 23.761 10.7402 23.6673 10.6464L23.3137 11ZM13.3593 20.2474C13.164 20.4426 13.164 20.7592 13.3593 20.9545C13.5545 21.1497 13.8711 21.1497 14.0664 20.9545L13.3593 20.2474ZM0.686305 10.5L0.186305 10.5V11.5L0.686305 11.5V10.5ZM13.5742 1.26072L13.2206 1.61428L22.9602 11.3536L23.3137 11L23.6673 10.6464L13.9277 0.907158L13.5742 1.26072ZM23.3137 11L22.9602 10.6464L13.3593 20.2474L13.7128 20.6009L14.0664 20.9545L23.6673 11.3536L23.3137 11ZM23.3137 11L23.3137 10.5L0.686305 10.5L0.686305 11L0.686305 11.5L23.3137 11.5L23.3137 11Z" fill="#FF5154" />
						</svg>
					</button>
				</div>


				<div class="swiper-pagination"></div>
			</div>
		</section>
	<?php endif; ?>

</div>
<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<div class="map">
	<a href="<?php opt_email_href('address') ?>" target="_blank" rel="nofollow">
	</a>
</div>
<div id="modal-fastbuy" class="modal"
	data-disclosure
	data-state="closed"
	data-overlay="true"
	data-esc-close="true"
	data-backdrop-close="true"
	data-focus-trap="true"
	data-restore-focus="true">
	<div class="modal__backdrop" data-backdrop></div>
	<div class="modal__dialog" data-panel tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="modal-title">
		<button class="modal__close" type="button" data-modal-close="#modal-fastbuy" aria-label="Закрыть">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
				<path d="M0.999756 1L22.6664 22.6667M22.6664 1L0.999756 22.6667" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</button>
		<form id="fastbuyproduct" data-vgform data-vgform-name='Форма заказать в один клик' class="feedback__form form-feedback">
			<h3 class="form-feedback__title h3">Заказать в один клик</h3>
			<div class="form-feedback__subtitle form-feedback__subtitle--product">Отправьте заявку на товар, и мы свяжемся с вами в ближайшее время.</div>

			<div class="form-feedback__product-name"><?= $productData['title'] ?></div>

			<div class="form-feedback__field">
				<label for="name" class="form-feedback__label">Как к вам обращаться?</label>
				<input type="text" name="name" id="name" placeholder="ФИО" class="form-feedback__input" data-vgform-label='Имя'>
			</div>
			<div class="form-feedback__field"><label for="phone" class="form-feedback__label">Номер телефона</label>
				<input data-vgform-label='Телефон' type="tel" name="phone" id="phone" placeholder="+7(999) 999-99-99" class="form-feedback__input">
			</div>
			<input type="hidden" data-vgform-label="Наименование товара" name="product_name" value="<?= $productData['title'] ?>">
			<input type="hidden" data-vgform-label="Ссылка на товар" name="product_href" value="<?= $current_url ?>">
			<input type="hidden" data-vgform-label="Артикул товара" name="product_sku" value="<?= !empty($productData['sku']) ? $productData['sku'] : ' ' ?>">
			<div class="checkbox form-feedback__field form-feedback__field--checkbox">
				<label class="checkbox__label">
					<input name="politics" type="checkbox" class="checkbox__input" data-vgform-agree data-vgform-exception>
					<span class="checkbox__box"></span>
					<span class="checkbox__text form-feedback__politics">Я согласен (-на) с <a href="#">политикой конфиденциальности</a> и даю согласие на обработку персональных данных</span>
				</label>
			</div>
			<button type="submit" class="form-feedback__btn btn btn--fill ">Отправить</button>
		</form>


	</div>
</div>

<?php get_footer(); ?>
