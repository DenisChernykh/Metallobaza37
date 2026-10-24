<?php

/**
 * Template Name: О компании
 */
get_header() ?>
<div class="container">

</div>
<section class="company-info">
	<div class="container">
		<div class="top company-info__top">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
			<h2 class="h2">О компании</h2>
		</div>
		<div class="company-info__content">
			<div class="company-info__image">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/about-page/company-info/1.webp" alt="Резка трубы">
				<div class="pattern"></div>
			</div>
			<div class="company-info__text-block">
				<div class="company-info__heading">

					<h3 class="h3"><?= esc_html(the_excerpt()) ?></h3>

				</div>
				<div class="company-info__text">
					<?= esc_html(the_content()) ?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="reasons">
	<div class="container">
		<div class="reasons-intro">
			<h1 class="reasons-intro__title h2">Почему выбирают<br>«Металлобазу Ивтент»</h1>
			<div class="pattern"></div>
			<p class="reasons-intro__subtitle">Оставьте заявку на обратный звонок – менеджер перезвонит в самое ближайшее время!</p>
			<button data-modal-open="#modal-contact" class="reasons-intro__button btn btn--fill">Заказать звонок</button>
		</div>

		<div class="features-grid features-grid--reasons">
			<article class="features-cards__item">
				<div class="features-cards__position">01</div>
				<div class="features-cards__title">Оперативная отгрузка без очередей</div>
				<p class="features-cards__text">Наличие широкого ассортимента, комплектация заявок под заказ. Простая покупка.</p>
			</article>
			<article class="features-cards__item">
				<div class="features-cards__position">02</div>
				<div class="features-cards__title">Доставка товара в день заказа</div>
				<p class="features-cards__text">Встретим, поможем, подскажем. Профессиональная консультация от опытных менеджеров. Мы ценим каждого покупателя!
				</p>
			</article>
			<article class="features-cards__item">
				<div class="features-cards__position">03</div>
				<div class="features-cards__title">Отгрузка металла в розницу</div>
				<p class="features-cards__text">Минимальная партия отгружаемого товара — всего 3 метра, возможно бесплатное хранение на базе по согласованию, погрузка и помощь с доставкой в день заказа.</p>
			</article>
			<div class="feature-card feature-card--image-desktop">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/about-page/reasons/1.webp" alt="Металлоконструкция" class="feature-card__image">
			</div>

		</div>
		<div class="feature-card feature-card--image-mb">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/img/about-page/reasons/1.webp" alt="Металлоконструкция" class="feature-card__image">
		</div>
	</div>
</section>
<?php get_template_part('template-parts/partners') ?>
<?php get_template_part('template-parts/reviews') ?>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>