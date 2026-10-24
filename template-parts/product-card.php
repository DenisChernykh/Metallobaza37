<?php

$product = $args;
$id = $product['id'] ?? '';
$product_id = $product['post_id'] ?? '';
$title = $product['title'] ?? '';
$sku = $product['sku'] ?? '';
$price = $product['price'] ?? 0;
$sale_price = $product['sale_price'] ?? 0;
$href = $product['href'] ?? '';
$unit = $product['unit']->name ?? 'м.п';
$mainImage = $product['main_image'];
if (empty($mainImage)) {
    $mainImage = '/wp-content/themes/metallobaza37/assets/img/no_image.png';
}
$has_option = empty($product['options']) ? "0" : "1";

$has_sale = $sale_price > 0 && $sale_price < $price;
?>

<article data-wp-context='{"productId": "<?= esc_attr($product_id)  ?>", "href": "<?= esc_attr($href)  ?>"}' data-stock="9" data-price="1500" data-product-id="<?php echo esc_attr($product_id) ?>" class="product-card">
	<a href="<?= $href ?>"
		class="stretched-link"
		aria-label="Открыть товар"></a>

	<div class="product-card__image">
		<div class="product-card__labels">
			<?php if ($has_sale): ?>
				<div class="label">Акция</div>
			<?php endif; ?>
			<?php if ($product['is_hit']): ?>
				<div class="label label--black">Хит</div>
			<?php endif; ?>
		</div>
		<div data-product-id="<?= $product_id ?>" class="favorite-icon favorite-icon--product">
			<?php the_icon('favorite') ?>
		</div>
		<img src="<?= $mainImage ?>" alt="<?= $title ?>">
	</div>

	<div class="product-card__info">
		<div class="product-card__title"><?= $title ?></div>
		<div class="product-card__footer">
			<div class="product-card__price">
				<div class="current"><?= number_format($has_sale ? $sale_price : $price, 0, '', ' ') ?> &#8381; <span><?= $unit ?></span></div>
				<?php if ($has_sale): ?>
					<div class="old"><?= number_format($price, 0, '', ' ') ?> &#8381; / <?= $unit ?></div>
				<?php endif; ?>
			</div>
			<!-- <button class="btn--black product-card__btn ">
				<span>В корзину</span>
				<?php the_icon('cart'); ?>
			</button> -->
			<div data-options="<?= $has_option ?>" class="product-list-item__quantity quantity">
				<button data-wp-bind--hidden="state.quantityForProduct" data-wp-on--click="actions.addCurrentProduct" class="btn--black btn--add-to-cart">
					<?php the_icon('cart'); ?>
					</button>
					<div data-wp-bind--hidden="!state.quantityForProduct" class="quantity-block">
						<button data-wp-on--click="actions.decForProduct" class="quantity__btn quantity__btn--minus">−</button>
						<input data-wp-on--change="actions.setQuantityForProductFromInput" data-wp-bind--value="state.quantityForProduct" size="3" type="number" class="quantity__input" value="0">
						<button data-wp-on--click="actions.incForProduct" class="quantity__btn quantity__btn--plus">+</button>
					</div>
				</div>
			</div>
	</div>
</article>
