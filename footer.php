<?php

$categoryList = $_SESSION['category_list'] ?? [];

?>
<footer class="footer">


	<div class="footer__top">
		<div class="container">
			<div class="footer__branding">
				<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="На главную">
					<img class="logo__img" src="<?php echo esc_url(wp_get_attachment_image_url(carbon_get_theme_option('site_logo_footer'), 'full')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
				</a>
				<div class="footer__action">
					<button data-modal-open="#modal-contact" class="footer__feedback btn btn--footer">Заказать звонок</button>
				</div>

			</div>
			<div class="footer__contacts">
				<div class="footer__company-info">
					<div class="footer__company-name">ООО Ивтент-сервис</div>
					<div class="footer__company-inn">ИНН 3702009352</div>
					<div class="footer__company-ogrn">ОГРН 1023700554833</div>
				</div>
				<div class="footer__contact-item">
					<h4 class="footer__contact-label h4">АДРЕС</h4>
					<a href="<?php opt_map_href('address') ?>" target="_blank" rel="nofollow" class="footer__contact-value underLink underLink--white">
						<?php the_opt('address'); ?>
					</a>
				</div>
				<div class="footer__contact-item">
					<h4 class="footer__contact-label h4">РЕЖИМ РАБОТЫ</h4>
					<div class="footer__contact-value footer__contact-value--time">
						<?php echo the_opt('work_time'); ?>
					</div>
				</div>
				<div class="footer__contact-item">
					<h4 class="footer__contact-label h4">ТЕЛЕФОНЫ ДЛЯ СВЯЗИ</h4>
					<div class="footer__contact-value">
						<?php $phone_1 = my_opt('phone_1'); ?>
						<?php $phone_2 = my_opt('phone_2'); ?>
						<a href="tel:<?php echo esc_attr(clean_phone($phone_1)); ?>" class="underLink underLink--white"><?php echo the_opt('phone_1'); ?></a><br />
						<a href="tel:<?php echo esc_attr(clean_phone($phone_2)); ?>" class="underLink underLink--white"><?php echo the_opt('phone_2'); ?></a>
					</div>
				</div>
				<div class="footer__contact-item--email">
					<h4 class="footer__contact-label h4">ЭЛЕКТРОННАЯ ПОЧТА</h4>
					<?php $email = my_opt('email'); ?>
					<a href="mailto:<?php echo esc_attr($email) ?>" class="footer__contact-value underLink underLink--white">
						<?php echo esc_html($email)  ?>
					</a>
				</div>
			</div>

		</div>
	</div>
	<div class="footer__bottom">
		<div class="pattern"></div>
		<div class="container">

			<?php wp_nav_menu([
				'theme_location' => 'topbar',
				'container'      => 'nav',
				'container_class' => 'footer__nav',
				'container_aria_label' => 'Верхнее меню',
				'menu_class'     => 'footer__nav-list',
				'li_class' => 'menu__item underLink',
				'a_class' => 'menu__link'

			]);
			?>
			<div data-disclosure data-mobile class="footer__catalog">
				<div data-toggle class="footer__catalog-title">
					<h3 class="h4">КАТАЛОГ</h3>
					<button
						class="footer__catalog-arrow"
						type="button">
						<?php the_icon('arrow-down-fill', 'icon--fill') ?>
					</button>
				</div>
                <div data-panel class="footer__catalog-columns">
                    <?php
                    $count = 0;
                    foreach ($categoryList as $cat):
                        if ($count % 4 === 0):
                            if ($count > 0) echo '</ul>';
                            echo '<ul class="footer__catalog-column">';
                        endif;
                        ?>
                        <li><a href="<?= esc_url($cat['link']) ?>" class="underLink"><?= esc_html($cat['name']) ?></a></li>
                        <?php
                        $count++;
                    endforeach;

                    if ($count > 0) echo '</ul>';
                    ?>
                </div>
			</div>
		</div>
	</div>
	<div class="footer__copyright">
		<div class="container">
			<div class="footer__legal">
				<a class="underLink underLink--white" href="#">Политика конфиденциальности</a>
				<span>© ООО Ивтент-сервис</span>
			</div>
			<a class="underLink underLink--white" href="https://victoryagency.ru/" class="footer__developer">
				Сайт разработан VICTORY group
			</a>
		</div>
	</div>

</footer>

<div id="modal-contact" class="modal"
	data-disclosure
	data-state="closed"
	data-overlay="true"
	data-esc-close="true"
	data-backdrop-close="true"
	data-focus-trap="true"
	data-restore-focus="true">
	<div class="modal__backdrop" data-backdrop></div>
	<div class="modal__dialog" data-panel tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="modal-title">
		<button class="modal__close" type="button" data-modal-close="#modal-contact" aria-label="Закрыть">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
				<path d="M0.999756 1L22.6664 22.6667M22.6664 1L0.999756 22.6667" stroke="#4F4D56" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</button>
		<form data-vgform data-vgform-name='Форма обратной связи' class="feedback__form form-feedback">
			<h3 class="form-feedback__title h3">Заказать звонок</h3>
			<div class="form-feedback__subtitle">Получите продукцию по выгодной цене с быстрой доставкой. Оставьте заявку — и мы подберём для вас оптимальное решение!</div>
			<div class="form-feedback__field">
				<label for="name" class="form-feedback__label">Как к вам обращаться?</label>
				<input type="text" name="name" id="name" placeholder="ФИО" class="form-feedback__input" data-vgfrom-label='Имя'>
			</div>
			<div class="form-feedback__field"><label for="phone" class="form-feedback__label">Номер телефона</label>
				<input data-vgfrom-label='Телефон' type="tel" name="phone" id="phone" placeholder="+7(999) 999-99-99" class="form-feedback__input">
			</div>
			<div class="checkbox form-feedback__field form-feedback__field--checkbox">
				<label class="checkbox__label">
					<input name="politics" type="checkbox" class="checkbox__input" data-vgform-agree data-vgform-exception>
					<span class="checkbox__box"></span>
					<span class="checkbox__text form-feedback__politics">Я согласен (-на) с <a href="#">политикой конфиденциальности</a> и даю согласие на обработку персональных данных</span>
				</label>
			</div>
			<button type="submit" class="form-feedback__btn btn btn--fill ">Заказать звонок</button>
		</form>


	</div>
</div>

<?php wp_footer(); ?>