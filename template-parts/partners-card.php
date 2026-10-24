<?php
$name = $args['name'] ?? 'Партнёр';
$index = $args['index'] ?? 1;
?>
<div class="partners__card">
	<img src=" <?php echo get_template_directory_uri(); ?>/assets/img/partners/<?php echo esc_attr($index); ?>.webp" alt="Партнер <?php echo esc_attr($name); ?>">
</div>