<section class="warranty">
	<div class="container">
		<div class="top company-info__top">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
			<h1 class="h2">Возврат и обмен</h1>
		</div>
		<div class="warranty__wrapper">
			<div class="warranty__content">
				<?php the_content() ?>
				<div class="delivery__contacts">
					<div class="delivery__contact">
						<div class="delivery__contact-label">Телефон для связи</div>
						<a href="tel:+<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>" class="delivery__contact-phone underLink"><?php the_opt('phone_1'); ?></a> <br>
						<a href="tel:+<?php echo esc_attr(clean_phone(my_opt('phone_2'))) ?>" class="delivery__contact-phone underLink"><?php the_opt('phone_2'); ?></a>

					</div>
					<div class="delivery__contact">
						<div class="delivery__contact-label">Электронная почта</div>
						<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="delivery__contact-mail underLink"><?php the_opt('email'); ?></a>

					</div>

				</div>
			</div>
			<div class="warranty__image">
				<img src=" <?php echo get_template_directory_uri() ?>/assets/img/warranty/1.webp" alt="Упаковка металлоконструкций">
			</div>
		</div>
	</div>


	</div>
</section>