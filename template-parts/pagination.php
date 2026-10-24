<!-- <nav class="pagination" aria-label="Пагинация"
	data-total="100" data-page="1" data-size="12" data-siblings="1">
	<button aria-label="Предыдущая" class="pagination__arrow pagination__arrow--prev"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
		</svg>
	</button>
	<ul class="pagination__list"></ul>
	<button aria-label="Следующая" class="pagination__arrow pagination__arrow--next"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
		</svg>
	</button>
</nav> -->

<?php
$q = $args['query'] ?? null;
if (!($q instanceof WP_Query) || $q->max_num_pages < 2) return;

$current  = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
$total    = (int) $q->max_num_pages;
$size     = (int) ($q->query_vars['posts_per_page'] ?? 0);
$siblings = (int) ($args['siblings'] ?? 1);

	$u = fn(int $n) => esc_url(get_pagenum_link($n));


	$pages = [];

	$start = max(1, $current - $siblings);
	$end   = min($total, $current + $siblings);


	$pages[] = 1;


	if ($start > 2) {
		$pages[] = 'dots-left';
	}


	for ($i = max(2, $start); $i <= min($end, $total - 1); $i++) {
		$pages[] = $i;
	}


	if ($end < $total - 1) {
		$pages[] = 'dots-right';
	}

	if ($total > 1) {
		$pages[] = $total;
	}
?>

<nav class="pagination" aria-label="Пагинация"
	data-total="<?php echo esc_attr($total); ?>"
	data-page="<?php echo esc_attr($current); ?>"
	data-size="<?php echo esc_attr($size); ?>"
	data-siblings="<?php echo esc_attr($siblings); ?>">

	<?php if ($current > 1): ?>
		<a aria-label="Предыдущая" class="pagination__arrow pagination__arrow--prev" href="<?php echo $u($current - 1); ?>">
			
			<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
	<?php else: ?>
		<span aria-label="Предыдущая" class="pagination__arrow pagination__arrow--prev" aria-disabled="true" tabindex="-1">
			<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</span>
	<?php endif; ?>

	<ul class="pagination__list">
		<?php foreach ($pages as $p): ?>
			<?php if ($p === 'dots-left' || $p === 'dots-right'): ?>
				<li><span class="pagination__link pagination__link--all">…</span></li>
			<?php else:
				$is_current = ($p === $current);
				$class = 'pagination__link' . ($is_current ? ' pagination__link--active' : '');
			?>
				<li>
					<?php if ($is_current): ?>
						<span class="<?php echo esc_attr($class); ?>" aria-current="page"><?php echo (int) $p; ?></span>
					<?php else: ?>
						<a class="<?php echo esc_attr($class); ?>" href="<?php echo $u($p); ?>"><?php echo (int) $p; ?></a>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>

	<?php if ($current < $total): ?>
		<a aria-label="Следующая" class="pagination__arrow pagination__arrow--next" href="<?php echo $u($current + 1); ?>">
			<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
	<?php else: ?>
		<span aria-label="Следующая" class="pagination__arrow pagination__arrow--next" aria-disabled="true" tabindex="-1">
			<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.866373 0.666992L6.19971 6.00033L0.866373 11.3337" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</span>
	<?php endif; ?>
</nav>