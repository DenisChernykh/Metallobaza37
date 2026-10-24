<?php $label = $args['label'] ?? 'Перейти в каталог';
$class = $args['class'] ?? '';
$href = $args['href'] ?? '#'; ?>

<a data-button href="<?= esc_attr($href) ?>" class="text-icon-button <?= esc_attr($class) ?>">
	<span data-icon class="btn-icon"><?php the_icon('arrow-left-up') ?>

	</span>
	<span data-text class="text"><?= esc_html($label) ?></span>


</a>