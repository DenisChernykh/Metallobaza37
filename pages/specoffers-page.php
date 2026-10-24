<?php

/**
 * Template Name: Спецпредложения
 */
get_header() ?>

<?php
require_once __DIR__ . "/../../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\CategoryService;
use VG_Catalog\Domain\Services\ProductService;
use VG_Catalog\Domain\UseCases\Product\Actions\GetListProductAction;
use VG_Catalog\Domain\UseCases\Product\Dto\FiltersProductDTO;
use VG_Catalog\Helpers\CategoryHelper;
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
		'items_qty' => array_sum(array_map(fn($r) => (int)($r['qty'] ?? 0), $cartData)),
	],
];

$cartData = $cartService->getList($paginationDTO);

// --- Инициализация сервисов ---
$categoryService = new CategoryService();
$productService  = new ProductService();

// --- Получение товаров категории ---
$page = $_GET['pagen'] ?? 1;
$limit = 15;
$currentFilterPrice = [
	'min' => $_GET['price_min'] ?? null,
	'max' => $_GET['price_max'] ?? null
];

$propertiesFilter = $_GET['prop'] ?? [];

$filters = [
	'limit' => $limit,
	'page' => $page,
	'has_sale' => true
];

// Добавляем фильтры по цене, если они есть
if (!empty($currentFilterPrice['min'])) {
	$filters['price_min'] = $currentFilterPrice['min'];
}
if (!empty($currentFilterPrice['max'])) {
	$filters['price_max'] = $currentFilterPrice['max'];
}

if (!empty($propertiesFilter)) {
	$filters['properties'] = $propertiesFilter;
}

if (!empty($_GET['sort']) && !empty($_GET['order'])) {
	$filters['sort_by'] = $_GET['sort'];
	$filters['sort_type'] = $_GET['order'];
}

$products = $productService->getList(
	new FiltersProductDTO($filters),
	new GetListProductAction()
);

$categoryHelper = new CategoryHelper();
$deep = true;
$categoryId = null;
$hasSale = true;
$filterData = $categoryHelper->getFilterData($categoryId, $deep, $currentFilterPrice, $hasSale);
$normalizedCart = metal_get_normalized_cart();
?>

<div class="container">

	<input type="hidden" name="favorite-page" value="1">
	<input type="hidden" name="page_specoffers" value="true">
	<section class="category">
		<div class="catalog-grid" aria-label="Категории каталога">
			<input type="hidden" name="current_category_id" value="<?= $categoryId ?>">

			<div class="catalog-grid__top">
				<?php
				if (function_exists('yoast_breadcrumb')) {
					yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
				}
				?>
				<h1 class="h2">Спецпредложения</h1>

			</div>
			<ul class="catalog-grid__list">
			</ul>
			<button class="btn">Показать еще</button>
			<?php if (!$products->isEmpty()): ?>
				<div class="category__top">
					<h2 class="h4">Фильтры товаров</h2>
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
							<select class="vgselect select-catalog select__title" name="sort" id="sort">
								<option id="select_id-1" value="sort=price&amp;order=ASC&amp;sort_id=1">Цена по возрастанию
								</option>
								<option id="select_id-2" value="sort=price&amp;order=DESC&amp;sort_id=2">Цена по убыванию</option>
								<option id="select_id-3" value="sort=discount&amp;order=DESC&amp;sort_id=3">По размеру скидки</option>
							</select>
						</div>
						<div class="mobile-filter-toggle">
							<span>Фильтры</span>
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 3L9 3M1 9H9M9 9V11M9 9V7M1 15H5M9 15L17 15M13 9H17M13 3L17 3M13 3V5M13 3V1M5.5 17V13" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
							</svg>

						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php wp_interactivity_state('cart',  ['cart' => $normalizedCart]);

			ob_start(); ?>
			<div data-wp-interactive="cart" class="category__wrapper">
				<?php if (!$products->isEmpty()): ?>
					<?php get_template_part('template-parts/product-page/filter', null, $filterData) ?>
				<?php endif; ?>

				<div class="product-list">
					<?php if (!$products->isEmpty()): ?>
						<?php
						foreach ($products as $product) {
							get_template_part('template-parts/product-list-item', null, $product);
						}
						?>
						<?php if ($products->lastPage() > 1): ?>
							<?php
							$current = $products->currentPage();
							$last = $products->lastPage();
							$start = max(1, $current - 2);
							$end = min($last, $current + 2);
							?>
							<div class="pagination">
								<button class="pagination__arrow pagination__arrow--prev"
									data-pagination-btn data-page="<?= max(1, $products->currentPage() - 1) ?>">
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
									data-pagination-btn data-page="<?= min($products->lastPage(), $products->currentPage() + 1) ?>">
									<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</button>
							</div>
						<?php endif; ?>
					<?php else: ?>
						<p>Товары не найдены.</p>
					<?php endif; ?>
				</div>
				<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>
				<?php if (!$products->isEmpty()): ?>
					<?php get_template_part('template-parts/product-page/product-grid', null, $products) ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
</div>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>