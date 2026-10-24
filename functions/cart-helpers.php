<?php
if (!defined('ABSPATH')) exit;

use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;

/**
 * Единая точка формирования состояния корзины для Interactivity API.
 */
function metal_get_normalized_cart(): array {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    // Гарантируем автозагрузку плагина каталога
    if (defined('WP_PLUGIN_DIR')) {
        $vg_autoload = WP_PLUGIN_DIR . '/VG_Catalog/vendor/autoload.php';
        if (file_exists($vg_autoload)) {
            require_once $vg_autoload;
        }
    }

    $service = new CartService();
    $cartData = $service->getList(new PaginationDTO(['limit' => 0]));

    $totalsBase = 0.0;
    $totalsFinal = 0.0;
    $itemsQty = 0;

    $items = array_map(function ($row) use (&$itemsQty, &$totalsBase, &$totalsFinal) {
        $qty   = (int)($row['quantity'] ?? 1);
        $p     = (float)($row['product']['price'] ?? 0);
        $s     = (float)($row['product']['sale_price'] ?? 0);
        $final = ($s > 0 && $s < $p) ? $s : $p;
        $unit  = $row['product']['unit']->name ?? 'м.п';

        $itemsQty += $qty;
        $totalsBase  += $p * $qty;
        $totalsFinal += $final * $qty;

        return [
            'id'        => (string)($row['id'] ?? ''),
            'productId' => (string)($row['product_id'] ?? 0),
            'name'      => (string)($row['product']['title'] ?? ''),
            'sku'       => (string)($row['product']['sku'] ?? ''),
            'quantity'  => $qty,
            'isHit'     => !empty($row['product']['is_hit']) && $row['product']['is_hit'] === '1',
            'hasSale'   => $s > 0 && $s < $p,
            'unit'      => '/ ' . $unit,
            // поля для текущей верстки/стора
            'price'                   => $row['product']['price'] ?? 0,
            'salePrice'               => $row['product']['sale_price'] ?? 0,
            'priceFormatted'          => number_format($final, 0, '', ' ') . ' ₽',
            'originalPriceFormatted'  => number_format($p, 0, '', ' ') . ' ₽',
        ];
    }, $cartData);

    $discount = max(0, $totalsBase - $totalsFinal);
    $fmt = static function (float $a): string {
        return number_format($a, 0, '', ' ') . ' ₽';
    };

    $cache = [
        'id'      => 'router',
        'version' => 0,
        'items'   => $items,
        'totals'  => [
            'items_qty' => $itemsQty,
            'sum_without_discount' => $totalsBase,
            'sum_with_discount'    => $totalsFinal,
            'discount'             => $discount,
            'sum_without_discount_formatted' => $fmt($totalsBase),
            'sum_with_discount_formatted'    => $fmt($totalsFinal),
            'discount_formatted'             => $fmt($discount),
        ],
    ];

    return $cache;
}

/**
 * Печатает состояние Interactivity для корзины.
 */
function metal_print_cart_state(): void {
    wp_interactivity_state('cart', ['cart' => metal_get_normalized_cart()]);
}

