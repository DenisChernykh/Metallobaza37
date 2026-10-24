<?php

$product = $args;

$product_id = $product['post_id'] ?? '';
$title = $product['title'] ?? '';
$sku = $product['sku'] ?? '';
$price = $product['price'] ?? 0;
$sale_price = $product['sale_price'] ?? 0;
$href = $product['href'] ?? '';
$unit = $product['unit']->name ?? 'м.п';

$has_option = empty($product['options']) ? "0" : "1";

$has_sale = $sale_price > 0 && $sale_price < $price;
?>

<article class="product-list-item" data-wp-context='{"productId": "<?= esc_attr($product_id)  ?>", "href": "<?= esc_attr($href)  ?>"}' data-product-id="<?= esc_attr($product_id) ?>" data-stock="9" data-price="1500">

	<a href="<?= $href ?>"
		class="stretched-link"
		aria-label="Открыть товар"></a>
	<div class="product-list-item__item product-list-item__item--name">
		<div class="product-list-item__name"><?= $title ?></div>
		<div class="product-list-item__labels">
			<?php if ($has_sale): ?>
				<div class="label">Акция</div>
			<?php endif; ?>
			<?php if ($product['is_hit']): ?>
				<div class="label label--black">Хит</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="product-list-item__item product-list-item__item--sku">
		<?php if (!empty($product['sku'])): ?>
			<div class="product-list-item__sku">Артикул: <?= $product['sku'] ?></div>
		<?php endif; ?>
	</div>
	<div class="product-list-item__item product-list-item__item--price">
		<div class="product-list-item__price">
			<div class="current"><?= number_format($has_sale ? $sale_price : $price, 0, '', ' ') ?> &#8381; <span><?= $unit ?></span></div>
			<?php if ($has_sale): ?>
				<div class="old"><?= number_format($price, 0, '', ' ') ?> &#8381; / <?= $unit ?></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="product-list-item__item product-list-item__item--icons">
		<button type="button" data-product-id="<?= $product_id ?>" class="favorite-icon favorite-icon--product">
			<?php the_icon('favorite') ?>
		</button>
		<div data-href="<?= $href ?>" data-options="<?= $has_option ?>" data-product-id="<?= $product_id ?>" data-cart-id="" class="product-list-item__quantity quantity" data-qty-control>
			<button data-wp-on--click="actions.addCurrentProduct" data-wp-bind--hidden="state.quantityForProduct" data-action="add" class="btn--black btn--add-to-cart">
				<?php the_icon('cart'); ?>
				</button>
				<div data-wp-bind--hidden="!state.quantityForProduct" class="quantity-block" data-qty-wrap>
					<button data-wp-on--click="actions.decForProduct" data-action="decrease" class="quantity__btn quantity__btn--minus">−</button>
					<input data-wp-on--change="actions.setQuantityForProductFromInput" data-wp-bind--value="state.quantityForProduct" size="3" type="number" data-qty-input class="quantity__input" value="0">
					<button data-wp-on--click="actions.incForProduct" class="quantity__btn quantity__btn--plus" data-action="increase">+</button>
				</div>
			</div>
		</div>

</article>
