<?php

/**
 * Template Name: Спасибо за заявку
 */
get_header() ?>

<div class="container thank-you">
	<div class="thank-you__row">
		<div class="thank-you__left">
			<h1 class="thank-you__title h1">Спасибо за заявку!</h1>
			<div class="thank-you__text">Получите продукцию по выгодной цене с быстрой доставкой. <br> Оставьте заявку - и мы подберём для вас оптимальное решение!</div>
			<a href=" <?php echo home_url() ?>" class="btn btn--fill">Вернуться на главную</a>
		</div>
		<div class="thank-you__right">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/img/thank-you/2.webp" alt="Металлоконструкции">
			<div class="pattern"></div>
		</div>
	</div>
</div>

<?php get_footer() ?>