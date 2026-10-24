<?php

/**
 * Template Name: Корзина */
get_header() ?>

<?php
function getProductWord($number)
{
    $cases = [2, 0, 1, 1, 1, 2];
    $titles = ['товар', 'товара', 'товаров'];
    return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

require_once __DIR__ . "/../../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Helpers\CategoryHelper;

$cartService  = new CartService();

$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartData = $cartService->getList($paginationDTO);
$cartCount = count($cartData);
$helper = new CategoryHelper();
$categoryId = 71;
$limit = 6;
$excludeIds = array_map(function ($item) {
    return $item['product_id'];
}, $cartData);
$random = true;



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
        $groupId   = (int) pick($opt, 'group_id', 0);
        $title     = (string) pick($opt, 'title', '');
        $priceDelta = (float) pick($opt, 'price', 0);
        $groupTitle = $groupsIndex[$groupId] ?? '';

        $selected[] = [
            'id'         => $id,
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
            'priceCut' => round($row['product']['price_cut'] ?? '5'),
            'priceFormatted' => number_format($final, 0, '', ' ') . ' ₽',
            'originalPriceFormatted' => number_format($p, 0, '', ' ') . ' ₽',
            'href' => $href,
            'options' => $selectedOptions ?? [],
            'optionsSummary'  => $optionsSummary,
            'optionsKey'      => $optionsKey,
            'rez' => (int)($row['rez'] ?? 0),

        ];
    }, $cartData),
    'totals'  => [
        'items_qty' => $cartTotals['qty'],
        'items_count' => $cartCount,
        'sum_without_discount' => $cartTotals['base'],
        'sum_with_discount'    => $cartTotals['final'],
        'discount'             => $cartTotals['discount'],
        'sum_without_discount_formatted' => number_format($cartTotals['base'], 0, '', ' ') . ' ₽',
        'sum_with_discount_formatted'    => number_format($cartTotals['final'], 0, '', ' ') . ' ₽',
        'discount_formatted' => number_format($cartTotals['discount'], 0, '', ' ') . ' ₽',
    ],
];

$excludeIds = [];
$otherProducts = $helper->getProductsByCategoryId($categoryId, true, $limit, $excludeIds, $random);

wp_interactivity_state('cart', ['cart' => $normalizedCart]);
ob_start();
?>
    <main data-wp-interactive="cart">
        <div class="section container cart">
            <div class="cart__top cart-top top">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
                }
                ?>
                <div class="cart-top__title">
                    <h1 class="h2">Корзина</h1>
                    <div data-wp-text="state.itemsCountText" data-wp-bind--hidden="!state.cart.items.length" class="cart-top__count"><?= $cartCount ?> <?= getProductWord($cartCount) ?></div>
                </div>
            </div>

            <div data-wp-bind--hidden="!state.cart.items.length" class="cart__row">
                <div data-wp-context='{"allChecked": true}' class="cart__left">
                    <div class="cart__buttons ">

                        <div class="checkbox cart-top__select-all">
                            <label class="checkbox__label">
                                <input data-wp-on--change="actions.toggleAllChecked" data-wp-bind--checked="state.areAllChecked" data-wp-bind--indeterminate="state.isIndeterminate" type="checkbox" class="checkbox__input">
                                <span class="checkbox__box"></span>
                                <span class="checkbox__text">Выбрать все</span>
                            </label>
                        </div>
                        <button data-wp-on--click="actions.removeSelected" data-wp-bind--disabled="state.isDeleteDisabled" class="cart-top__delete">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.90039 6.6H20.1004M18.3004 6.6V19.2C18.3004 20.1 17.4004 21 16.5004 21H7.50039C6.60039 21 5.70039 20.1 5.70039 19.2V6.6M8.40039 6.6V4.8C8.40039 3.9 9.30039 3 10.2004 3H13.8004C14.7004 3 15.6004 3.9 15.6004 4.8V6.6" stroke="#4F4D56" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Удалить выбранное</span>
                        </button>
                    </div>
                    <div data-cart-row class="cart__items">

                        <template data-wp-each--items="state.cart.items" data-wp-each-key="context.items.id">
                            <article class="product-list-item cart-list-item">
                                <a data-wp-bind--href="context.items.href"
                                   class="stretched-link"
                                   aria-label="Открыть товар"></a>
                                <div class="cart-list-item__checkbox checkbox">
                                    <label class="checkbox__label">
                                        <input data-wp-on--change="actions.toggleCheck" data-wp-bind--checked="state.isCurrentItemChecked" type="checkbox" class="checkbox__input">
                                        <span class="checkbox__box"></span>
                                        <span class="checkbox__text"></span>
                                    </label>

                                </div>
                                <div class="product-list-item__item product-list-item__item--name">
                                    <div data-wp-text="context.items.name" class="product-list-item__name"></div>
                                    <div class="product-list-item__labels">
                                        <div data-wp-bind--hidden="!context.items.hasSale" class="label">Акция</div>
                                        <div data-wp-bind--hidden="!context.items.isHit" class="label label--black">Хит</div>

                                    </div>

                                    <div class="product-list-item__options">
                                        <template
                                                data-wp-each--opt="context.items.options"
                                                data-wp-each-key="context.opt.id">
                                            <div class="option-item">
                                                <p class="option-name" data-wp-text="context.opt.groupTitle"></p>
                                                <p class="option-value" data-wp-text="context.opt.title"></p>
                                            </div>
                                        </template>
                                    </div>



                                    <div data-wp-bind--hidden="!context.items.rez" class="cart-list-item__additional">
                                        Рез - <span data-wp-text="context.items.priceCut"></span> ₽/м.п.
                                    </div>
                                </div>
                                <div class="product-list-item__options">


                                </div>
                                <div class="product-list-item__item product-list-item__item--price">
                                    <div class="product-list-item__price">
                                        <!-- <div class="current"><?= number_format($has_sale ? $sale_price : $price, 0, '', ' ') ?> &#8381; <span><?= $unit ?></span></div> -->

                                        <!-- <?php if ($has_sale): ?>
											<div class="old"><?= number_format($price, 0, '', ' ') ?> &#8381; / <?= $unit ?></div>
										<?php endif; ?> -->
                                    </div>
                                    <div class="product-list-item__price">
                                        <div class="current">
                                            <span data-wp-text="context.items.priceFormatted"></span>
                                            <span class="unit" data-wp-text="context.items.unit"></span>
                                        </div>
                                        <div
                                                class="old"
                                                data-wp-bind--hidden="!context.items.hasSale">
										<span data-wp-text='context.items.originalPriceFormatted'>
										</span>
                                            <span class="unit" data-wp-text="context.items.unit"></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="product-list-item__item product-list-item__item--icons">
	                                    <div class="product-list-item__quantity quantity" data-qty-control>
	                                        <button data-wp-on--click="actions.decQuantity" class="quantity__btn quantity__btn--minus">-</button>
	                                        <input data-wp-on--change="actions.setQuantityForCartItemFromInput" data-wp-bind--value="context.items.quantity" size="3" type="number" data-qty-input class="quantity__input" value="0">
	                                        <button data-wp-on--click="actions.incQuantity" class="quantity__btn quantity__btn--plus">+</button>
	                                    </div>
                                    <div class="cart-list-item__icons">
                                        <button type="button" data-wp-bind--data-product-id="context.items.productId" class="favorite-icon favorite-icon--product">
                                            <?php the_icon('favorite') ?>
                                        </button>

                                        <button data-action="remove" data-wp-on--click="actions.removeItem" class="button trash-icon"><svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.0625 6.875H20.9375M19.0625 6.875V20C19.0625 20.9375 18.125 21.875 17.1875 21.875H7.8125C6.875 21.875 5.9375 20.9375 5.9375 20V6.875M8.75 6.875V5C8.75 4.0625 9.6875 3.125 10.625 3.125H14.375C15.3125 3.125 16.25 4.0625 16.25 5V6.875" stroke="#100F15" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                        </button>

                                    </div>


                                </div>
                            </article>
                        </template>
                    </div>
                </div>

                <div class="cart__right">
                    <div class="cart-summary">
                        <div class="cart-summary__row cart-summary__row--total">
                            <span class="cart-summary__label h3">Итого</span>
                            <span data-wp-text="state.cart.totals.sum_with_discount_formatted" class="cart-summary__price--big-price"></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--quantity">
						<span class="cart-summary__label">
							Товары,
							<span
                                    data-wp-text="state.cart.totals.items_count"><?= $cartCount ?></span>
							шт
						</span>
                            <span data-wp-text="state.cart.totals.sum_without_discount_formatted" class="cart-summary__value"></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--discount">
                            <span class="cart-summary__label">Скидка</span>
                            <span data-wp-text="state.cart.totals.discount_formatted" class="cart-summary__value"></span>
                        </div>
                        <a href="/checkout" class="cart-summary__btn btn btn--fill">Оформить заказ</a>
                    </div>
                    <div class="product__delivery product-delivery">
                        <div class="product-delivery__title">Варианты доставки</div>
                        <div class="product-delivery__row">
                            <div class="product-delivery__item">
                                <div class="product-delivery__type">Самовывоз в Иваново</div>
                                <div class="product-delivery__price">Бесплатно</div>
                            </div>
                            <div class="product-delivery__item">
                                <div class="product-delivery__type">Доставка курьером по Иваново <span>Обсуждается отдельно с менеджером</span></div>
                                <div class="product-delivery__price">от 1 600 ₽</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div data-wp-bind--hidden="state.cart.items.length" class="cart__empty">
                В корзине сейчас нет товаров, перейдите в одну из категорий:
            </div>
        </div>
        <section class="promotions" aria-label="Категории каталога">

            <div class="container">
                <div class="catalog-grid__top">
                    <h2 data-wp-text="state.promoTitle" class="h2"></h2>
                </div>
                <div class="slider">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($otherProducts as $product): ?>
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
            </div>
        </section>
        <?php get_template_part('template-parts/feedback') ?>
        <?php get_template_part('template-parts/contacts') ?>
    </main>
<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>
<?php get_footer()  ?>
