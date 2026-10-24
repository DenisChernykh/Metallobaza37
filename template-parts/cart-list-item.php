<?php
$cartItem = $args;
$product = $cartItem['product'] ?? [];
$price = $product['price'] ?? 0;
$sale_price = $product['sale_price'] ?? 0;
$href = $product['href'] ?? '';
$unit = $product['unit']->name ?? 'м.п';
$has_sale = $sale_price > 0 && $sale_price < $price;
$rez = $cartItem['rez'] ?? 0;
$resultOptions = [];

if (isset($cartItem['options']) && is_array($cartItem['options'])) {
	foreach ($cartItem['product']['options'] as $cartOption) {
		if (isset($cartOption['options'][0]['group_id'])) {

			$groupId = $cartOption['options'][0]['group_id'];
			$optionTitle = $cartOption['options'][0]['title'] ?? '';

			$groupTitle = '';
			if (isset($product['options']) && is_array($product['options'])) {
				foreach ($product['options'] as $productOptionGroup) {
					if (isset($productOptionGroup['post_id']) && $productOptionGroup['post_id'] == $groupId) {
						$groupTitle = $productOptionGroup['title'] ?? '';
						break;
					}
				}
			}

			if ($groupTitle && $optionTitle) {
				$resultOptions[] = [
					'group_title' => $groupTitle,
					'option_title' => $optionTitle
				];
			}
		}
	}
}

?>


<article data-product-id="<?= $cartItem['product_id'] ?>" data-wp-context='{"itemId": "<?= $cartItem['id'] ?>"}' data-cart-id="<?= $cartItem['id']  ?>" data-wp-bind--hidden="!selectors.itemExistsForItem" class="product-list-item cart-list-item">
	<a href="<?= $href ?>"
		class="stretched-link"
		aria-label="Открыть товар"></a>
	<div class="cart-list-item__checkbox checkbox">

		<label class=" checkbox__label">
			<input data-wp-on--change="actions.toggleCheck" data-wp-bind--checked="context.checked" type="checkbox" class="checkbox__input">
			<span class="checkbox__box"></span>
			<span class="checkbox__text"></span>
		</label>

	</div>
	<div class="product-list-item__item product-list-item__item--name">
		<div class="product-list-item__name"><?= $product['title'] ?></div>
		<?php if ($rez > 0): ?>
			<div class="rez-info">Требуется рез - 5 ₽/п.м.</div>
		<?php endif; ?>
		<div class="product-list-item__labels">
			<?php if ($has_sale): ?>
				<div class="label">Акция</div>
			<?php endif; ?>
			<?php if ($product['is_hit']): ?>
				<div class="label label--black">Хит</div>
			<?php endif; ?>
		</div>
		<?php if (!empty($resultOptions)): ?>
			<div class="product-list-item__options">
				<?php foreach ($resultOptions as $option): ?>
					<div class="option-item">
						<p class="option-name"><?= $option['group_title'] ?></p>
						<p class="option-value"><?= $option['option_title'] ?></p>
					</div>
				<?php endforeach; ?>
			</div>
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
		<div class="product-list-item__quantity quantity" data-qty-control>
			<button data-wp-on--click="actions.decQuantity" class="quantity__btn quantity__btn--minus">-</button>
			<input data-wp-bind--value="state.quantityForCartItem" size="3" type="number" data-qty-input class="quantity__input" value="0">
			<button data-wp-on--click="actions.incQuantity" class="quantity__btn quantity__btn--plus">+</button>
		</div>
		<div class="cart-list-item__icons">
			<button type="button" data-product-id="<?= $cartItem['product_id'] ?>" class="favorite-icon favorite-icon--product">
				<?php the_icon('favorite') ?>
			</button>
			<button data-action="remove" data-wp-on--click="actions.removeItem" class="button trash-icon"><svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M4.0625 6.875H20.9375M19.0625 6.875V20C19.0625 20.9375 18.125 21.875 17.1875 21.875H7.8125C6.875 21.875 5.9375 20.9375 5.9375 20V6.875M8.75 6.875V5C8.75 4.0625 9.6875 3.125 10.625 3.125H14.375C15.3125 3.125 16.25 4.0625 16.25 5V6.875" stroke="#100F15" stroke-linecap="round" stroke-linejoin="round" />
				</svg>

			</button>
		</div>


	</div>
</article>