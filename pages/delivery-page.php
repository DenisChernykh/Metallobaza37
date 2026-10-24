<?php

/**
 * Template Name: Доставка
 */
get_header() ?>

<section class="delivery">
	<div class="container">
		<div class="top company-info__top">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
			}
			?>
			<h1 class="h2">Доставка</h1>
		</div>
		<div class="delivery__wrapper">
			<div class="delivery__content">

				<div class="delivery__text">
					<?php the_content() ?>

				</div>
				<?php $pid = get_the_ID();

				$section_title = carbon_get_post_meta($pid, 'section_title');
				$delivery_groups = carbon_get_post_meta($pid, 'delivery_groups');
				?>
				<div class="delivery__city">
					<?php if ($section_title): ?>
						<h4 class="delivery__subtitle h4"><?= esc_html($section_title) ?></h4>
					<?php endif; ?>
					<?php if ($delivery_groups):
						foreach ($delivery_groups as $group): ?>
							<div class="delivery-group">
								<div class="delivery-group__title">
									<?= esc_html($group['group_title']) ?>
								</div>
								<?php if (!empty($group['group_rows'])):
									foreach ($group['group_rows'] as $row): ?>

										<div class="delivery-group__row">
											<div class="delivery-group__desc"><?= esc_html($row['row_desc']) ?></div>
											<div class="delivery-group__desh"></div>
											<div class="delivery-group__price"><?= esc_html($row['row_price']) ?></div>
										</div>
								<?php endforeach;
								endif; ?>

							</div>


					<?php endforeach;
					endif; ?>

				</div>

				<div class="delivery__region">
					<h4 class="delivery__subtitle h4">Доставка по области</h4>
					<p class="delivery__paragraph">
						<?= esc_html(get_the_excerpt()); ?>
					</p>
					<div class="delivery__contacts">
						<div class="delivery__contact">
							<div class="delivery__contact-label">Телефон для связи</div>
							<a href="tel:+<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>" class="delivery__contact-phone"><?php echo the_opt('phone_1'); ?></a> <br>
							<a href="tel:+<?php echo esc_attr(clean_phone(my_opt('phone_2'))) ?>" class="delivery__contact-phone"><?php echo the_opt('phone_2'); ?></a>

						</div>
						<div class="delivery__contact">
							<div class="delivery__contact-label">Электронная почта</div>
							<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="delivery__contact-mail"><?php echo the_opt('email'); ?></a>

						</div>

					</div>
				</div>
				<?php $pay_groups = carbon_get_post_meta($pid, 'pay_groups'); ?>
				<?php $pay_method = carbon_get_post_meta($pid, 'pay_method'); ?>

				<div class="delivery__payment">
					<h3 class="delivery__payment-subtitle h2">Оплата</h3>
					<h4 class="delivery__subsubtitle h4">Удобные и надёжные способы оплаты</h4>

					<ul class="delivery__list">
						<?php foreach ($pay_groups as $group) {
							echo '<li class="delivery__item">' . esc_html($group['pay_method']) . '</li>';
						} ?>

					</ul>
				</div>
			</div>
			<div class="delivery__image">
				<img src=" <?php echo get_template_directory_uri() ?>/assets/img/delivery/1.webp" alt="Грузовик с логотипом Ивтент">
			</div>
		</div>
	</div>
</section>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>