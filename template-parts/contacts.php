<section class="contacts">
	<div class="container">
		<div class="top">
			<h2 class="h2">Контактная информация</h2>
		</div>
		<div class="contacts__cards cards-contacts">
			<article class="contacts-card">
				<div class="contacts-card__point"></div>
				<h4 class="contacts-card__title h4">Адрес</h4>
				<div class="contacts-card__text"><a href="<?php opt_map_href('address') ?>" class="underLink"><?php the_opt_br('address'); ?>
					</a>
				</div>
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
					<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="underLink"><?php echo carbon_get_theme_option('email'); ?>
					</a>
				</div>
				<div class="contacts-card__line"></div>
			</article>

		</div>
	</div>

	<div class="map" data-map>
		<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A035c2b41862637d5828d803fe0a818a3dbcdf69624c73a25be7e2626d98d7d92&amp;source=constructor" width="100%" height="100%" frameborder="0"></iframe>
	</div>
</section>