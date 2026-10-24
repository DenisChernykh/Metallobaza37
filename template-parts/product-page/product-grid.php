<?php
require_once __DIR__ . "/../../../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;

$cartService  = new CartService();

$paginationDTO = new PaginationDTO(['limit' => 0]);
$cartData = $cartService->getList($paginationDTO);


$products = $args;
?>



<div class="product-grid">

	<?php
	$limit = 15;
	foreach ($products as $product) {
		get_template_part('template-parts/product-card', null, $product);
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

	<!--	<div class="pagination">-->
	<!--		<button class="pagination__arrow pagination__arrow--prev"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">-->
	<!--				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />-->
	<!--			</svg>-->
	<!--		</button>-->
	<!---->
	<!--		<a href="#" class="pagination__link pagination__link--active">1</a>-->
	<!--		<a href="#" class="pagination__link pagination__link--next">2</a>-->
	<!--		<a href="#" class="pagination__link">3</a>-->
	<!--		<a href="#" class="pagination__link pagination__link--all">...</a>-->
	<!--		<a href="#" class="pagination__link">100</a>-->
	<!--		<button class="pagination__arrow pagination__arrow--next"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">-->
	<!--				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />-->
	<!--			</svg>-->
	<!--		</button>-->
	<!--	</div>-->
</div>
