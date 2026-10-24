<?php

/**
 * Template Name: Контакты
 */
get_header() ?>

<section class="contacts">
	<div class="container">
		<div class="top company-info__top">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
			<h2 class="h2">Контакты</h2>
		</div>
		<div class="contacts__cards cards-contacts">
			<article class="contacts-card">
				<div class="contacts-card__point"></div>
				<h4 class="contacts-card__title h4">Адрес</h4>
				<div class="contacts-card__text">
					<a href="<?php opt_map_href('address') ?>" class="underLink"><?php the_opt_br('address'); ?>
				</div>
				</a>

				<div class="contacts-card__line"></div>

			</article>
			<article class="contacts-card">
				<div class="contacts-card__point"></div>
				<h4 class="contacts-card__title" h4>Режим работы</h4>
				<div class="contacts-card__text"><?php the_opt_br('work_time'); ?></div>
				<div class="contacts-card__line"></div>
			</article>
			<article class="contacts-card">
				<div class="contacts-card__point"></div>
				<h4 class="contacts-card__title h4">Телефоны для связи</h4>
				<div class="contacts-card__text ">
					<a class="underLink" href="tel:+<?php esc_attr(clean_phone(my_opt('phone_1'))) ?>"><?php echo the_opt('phone_1'); ?></a> <br>
					<a class="underLink" href="tel:+<?php esc_attr(clean_phone(my_opt('phone_2'))) ?>"><?php echo the_opt('phone_2'); ?></a>
				</div>
				<div class="contacts-card__line"></div>
			</article>
			<article class="contacts-card">
				<div class="contacts-card__point"></div>
				<h4 class="contacts-card__title h4">Электронная почта</h4>
				<div class="contacts-card__text">
					<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="underLink"><?php echo carbon_get_theme_option('email'); ?></a>
				</div>
				<div class="contacts-card__line"></div>
			</article>

		</div>
	</div>

	<div class="map" data-map>
		<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A035c2b41862637d5828d803fe0a818a3dbcdf69624c73a25be7e2626d98d7d92&amp;source=constructor" width="100%" height="100%" frameborder="0"></iframe>
	</div>
</section>
<section class="contacts-data">

	<div class="container">
		<div class="top">
			<h2 class="h2">Реквизиты</h2>
		</div>
		<div class="contacts-data__grid">
			<?php
			$id = get_the_ID();
			$val = fn($k) => trim((string) carbon_get_post_meta($id, $k));
			?>
			<div class="pattern"></div>
			<div class="contacts-data__column">
				<p class="contacts-data__label">Фактический адрес</p>
				<p class="contacts-data__value"><?= esc_html($val('org_address')); ?></p>

				<p class="contacts-data__label">ОГРН</p>
				<p class="contacts-data__value"><?= esc_html($val('org_ogrn')); ?></p>

				<p class="contacts-data__label">ИНН / КПП</p>
				<p class="contacts-data__value"><?= esc_html($val('org_inn')); ?></p>

				<p class="contacts-data__label">ОКПО</p>
				<p class="contacts-data__value"><?= esc_html($val('org_okpo')); ?></p>
			</div>

			<div class="contacts-data__column">
				<p class="contacts-data__label">ОКАТО</p>
				<p class="contacts-data__value"><?= esc_html($val('org_okato')); ?></p>

				<p class="contacts-data__label">ОКВЭД</p>
				<p class="contacts-data__value"><?= esc_html($val('org_okved')); ?></p>

				<p class="contacts-data__label">Расчетный счет</p>
				<p class="contacts-data__value"><?= esc_html($val('org_rs')); ?></p>

				<p class="contacts-data__label">Корсчет</p>
				<p class="contacts-data__value"><?= esc_html($val('org_ks')); ?></p>
			</div>

			<div class="contacts-data__column">
				<p class="contacts-data__label">Наименование банка</p>
				<p class="contacts-data__value"><?= esc_html($val('org_bank_name')); ?></p>

				<p class="contacts-data__label">БИК</p>
				<p class="contacts-data__value"><?= esc_html($val('org_bik')); ?></p>

				<p class="contacts-data__label">ФИО директора</p>
				<p class="contacts-data__value"><?= esc_html($val('org_ceo')); ?></p>

				<p class="contacts-data__label">Электронная почта</p>
				<p class="contacts-data__value">
					<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="contacts-data__email underLink"><?php the_opt('email'); ?></a>
				</p>
			</div>
		</div>
	</div>
</section>
<?php get_template_part('template-parts/feedback') ?>
<?php get_footer()  ?>