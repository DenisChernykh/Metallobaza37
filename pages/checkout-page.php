<?php

/**
 * Template Name: Оформление заказа
 */
get_header();
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

function buildOptionsList($product)
{
    $groups = $product['options'] ?? [];
    if (!is_array($groups) || !$groups) return [];

    $groupTitleById = [];
    foreach ($groups as $g) {
        $gid = (int)($g['post_id'] ?? 0);
        if ($gid) $groupTitleById[$gid] = (string)($g['title'] ?? '');
    }

    $out = [];
    foreach ($groups as $g) {
        $selected = $g['options'] ?? null;
        if (!$selected) continue;

        $selectedList = (isset($selected[0]) && is_array($selected[0])) ? $selected : [$selected];

        foreach ($selectedList as $opt) {
            if (!is_array($opt)) continue;
            $gid        = (int)($opt['group_id'] ?? 0);
            $optionName = (string)($opt['title'] ?? '');
            $groupName  = $groupTitleById[$gid] ?? '';
            if ($gid && $optionName !== '' && $groupName !== '') {
                $out[] = [
                    'groupTitle'  => $groupName,
                    'optionTitle' => $optionName,
                ];
            }
        }
    }
    return $out;
}

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
        $groupTitle = '';
        $optionTitle = '';
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
            'options'   => buildOptionsList($row['product']),
            'href' => $href,
            'selectedOptions' => $selectedOptions,
            'optionsSummary'  => $optionsSummary,
            'optionsKey'      => $optionsKey,
            'rez' => (int)($row['rez'] ?? 0)
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
//$normalizedCart = metal_get_normalized_cart();

$cartProducts = $normalizedCart['items'];

$excludeIds = [];
foreach ($cartData as $cartItem) {
    $product = $cartItem['product'] ?? [];
    if (isset($cartItem['product']['options']) && is_array($cartItem['product']['options'])) {
        foreach ($cartItem['product']['options'] as $cartOption) {
            $options = $cartOption['options'][0] ?? null;

            if (isset($options)) {
                $groupId = $options['group_id'];
                $optionTitle = $options['title'] ?? '';
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
                        'option_title' => $optionTitle,

                    ];
                }
            }
        }
    }
}

$otherProducts = $helper->getProductsByCategoryId($categoryId, true, $limit, $excludeIds, $random);


wp_interactivity_state('cart', ['cart' => $normalizedCart]);

ob_start();
?>

    <div data-wp-interactive="cart" class="section container cart">
        <input type="hidden" name="page_checkout" value="1">
        <div class="cart__top cart-top top">
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
            }
            ?>
            <div class="cart-top__title">
                <h1 class="h2">Оформление заказа</h1>
                <div data-wp-text="state.itemsCountText" data-wp-bind--hidden="!state.cart.items.length" class="cart-top__count"><?= $cartCount ?> <?= getProductWord($cartCount) ?></div>


            </div>
        </div>

        <div class="cart__row">
            <div class="cart__left">
                <form id="checkoutproducts" data-vgform data-vgform-name='Оформление заказа' class="checkout-form">
                    <h3 class="checkout-form__title h3">Оставьте контактные данные</h3>
                    <div class=" checkout-form__field">
                        <label for="name" class="form-feedback__label checkout-form__label">Как к вам обращаться?</label>
                        <input data-vgfrom-label='Имя' type="text" name="name" id="name" placeholder="ФИО" class="form-feedback__input checkout-form__input">
                        <div class="error"></div>
                    </div>
                    <div class="checkout-form__wrapper">
                        <div class=" checkout-form__field">
                            <label for="phone" class="form-feedback__label checkout-form__label">Номер телефона</label>
                            <input data-vgfrom-label='Телефон' type="tel" name="phone" id="phone" placeholder="+7 (999) 999-99-99" class="form-feedback__input checkout-form__input">
                        </div>
                        <div class=" checkout-form__field">
                            <label for="phone" class="form-feedback__label checkout-form__label">Электронная почта</label>
                            <input data-vgfrom-label='Почта' type="text" name="email" id="email" placeholder="example@gmail.com" class="form-feedback__input checkout-form__input">
                        </div>
                    </div>
                    <div hidden="hidden">
                        <label for="products_order" class="form-feedback__label checkout-form__label">Список товаров</label>
                        <input type="hidden" data-vgfrom-label='Заказ' name="products_order" id="products_order" value='<?= json_encode($normalizedCart)?>'>
                    </div>

                    <div class="checkbox">

                        <label class=" checkbox__label">
                            <input type="checkbox" name="agree" class="checkbox__input" data-vgform-agree data-vgform-exception>
                            <span class="checkbox__box"></span>
                            <span class="checkbox__text form-feedback__politics">Я согласен (-на) с <a  href="#">политикой конфиденциальности</a> и даю согласие на обработку персональных данных</span>
                        </label>

                    </div>
                    <button type="submit" class="btn btn--fill btn--long">Оформить заказ</button>
                    <div class="checkout-form__text">После нажатия кнопки «Оформить заказ» по указанному номеру позвонит менеджер магазина, чтобы подтвердить заказ.</div>


                </form>
            </div>
            <div class="cart__right">
                <div class="cart-summary">
                    <div class="cart-summary__row cart-summary__row--total">
                        <span class="cart-summary__label h3">Итого</span>
                        <span class="cart-summary__price--big-price"><?= esc_html($normalizedCart['totals']['sum_with_discount_formatted']) ?></span>
                    </div>
                    <div class="cart-summary__row cart-summary__row--quantity">
                        <span class="cart-summary__label">Товары, <?= $cartCount ?> шт</span>
                        <span class="cart-summary__value"><?= esc_html($normalizedCart['totals']['sum_without_discount_formatted']) ?></span>
                    </div>
                    <div class="cart-summary__row cart-summary__row--discount">
                        <span class="cart-summary__label">Скидка</span>
                        <span class="cart-summary__value">-<?= esc_html(number_format((float)$normalizedCart['totals']['discount'], 0, '.', ' ')) ?> ₽</span>
                    </div>
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
    </div>
<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>


	<?php get_footer()  ?>
