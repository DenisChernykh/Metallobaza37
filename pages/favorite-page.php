<?php

/**
 * Template Name: Избранное
 */
get_header() ?>

<?php

require_once __DIR__ . "/../../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\FavoriteService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\Services\CartService;

$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartService  = new CartService();
$cartData = $cartService->getList($paginationDTO);
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
		'items_qty' => array_sum(array_map(fn($r) => (int)($r['quantity'] ?? 0), $cartData)),
	],
];
$normalizedCart = metal_get_normalized_cart();

$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartData = $cartService->getList($paginationDTO);

$favoriteService = new FavoriteService();

$page = $_GET['pagen'] ?? 1;
$limit = 15;
$favoriteList = $favoriteService->getList(new PaginationDTO(['limit' => $limit, 'page' => $page,]));

?>

<section class="delivery">
	<div class="container">
		<div class="top company-info__top">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
			<h1 class="h2">Избранное</h1>
		</div>
		<?php metal_print_cart_state();

		ob_start(); ?>
		<div data-wp-interactive="cart" class="product-grid">
			<input type="hidden" name="favorite-page" value="1">
			<?php if (!$favoriteList->isEmpty()): ?>
				<?php foreach ($favoriteList as $favorite): ?>
					<?php get_template_part('template-parts/product-card', null, $favorite); ?>
				<?php endforeach; ?>
				<?php if ($favoriteList->lastPage() > 1): ?>
					<?php
					$current = $favoriteList->currentPage();
					$last = $favoriteList->lastPage();
					$start = max(1, $current - 2);
					$end = min($last, $current + 2);
					?>
					<div class="pagination">
						<button class="pagination__arrow pagination__arrow--prev"
							data-pagination-btn data-page="<?= max(1, $favoriteList->currentPage() - 1) ?>">
							<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>

						<!-- Первая страница -->
						<?php if ($start > 1): ?>
							<a data-pagination-btn data-page="1" href="" class="pagination__link">1</a>
							<?php if ($start > 2): ?>
								<a href="#" class="pagination__link pagination__link--all">...</a>
							<?php endif; ?>
						<?php endif; ?>

						<!-- Промежуточные страницы -->
						<?php for ($i = $start; $i <= $end; $i++): ?>
							<a data-pagination-btn data-page="<?= $i ?>" href="" class="pagination__link <?= $i == $current ? 'pagination__link--active' : '' ?>">
								<?= $i ?>
							</a>
						<?php endfor; ?>

						<!-- Последняя страница -->
						<?php if ($end < $last): ?>
							<?php if ($end < $last - 1): ?>
								<a href="#" class="pagination__link pagination__link--all">...</a>
							<?php endif; ?>
							<a data-pagination-btn data-page="<?= $last ?>" href="" class="pagination__link"><?= $last ?></a>
						<?php endif; ?>
						<button class="pagination__arrow pagination__arrow--next"
							data-pagination-btn data-page="<?= min($favoriteList->lastPage(), $favoriteList->currentPage() + 1) ?>">
							<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<p>В избранном нет товаров.</p>
			<?php endif; ?>
		</div>
		<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>
	</div>
</section>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>
