<?php


get_header() ?>


<?php if (have_posts()): while (have_posts()): the_post() ?>
		<div class="container">
			<section class="article">
				<div class="catalog-grid">

					<div class="catalog-grid__top">
						<?php
						if (function_exists('yoast_breadcrumb')) {
							yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
						}
						?>
						<h1 class="h2 article__title"><?php echo esc_html(get_the_title()) ?></h1>
						<time class="article__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>




					</div>
					<div class="container">
						<div class="article__row">
							<div class="article__left">
								<div class="article__image">

									<?php if (has_post_thumbnail()) :
										$id  = get_post_thumbnail_id();
										$alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: get_the_title();


										echo wp_get_attachment_image(
											$id,
											'news-thumb',
											false,
											[
												'alt'   => esc_attr($alt),
												'class' => 'news-card__img',
											]
										);
									else : ?>
										<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/fallback-news.webp'); ?>"
											alt="<?php echo esc_attr(get_the_title()); ?>">
									<?php endif; ?>

								</div>
								<div class="article__contacts">
									<p class="article__text">Всегда на связи для вашего удобства!
										Звоните или пишите – специалисты готовы проконсультировать вас.</p>
								</div>
								<div class="delivery__contacts">
									<div class="delivery__contact">
										<div class="delivery__contact-label">Телефон для связи</div>
										<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_1'))) ?>" class="delivery__contact-phone underLink"><?php the_opt('phone_1'); ?></a> <br>
										<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_2'))) ?>" class="delivery__contact-phone underLink"><?php the_opt('phone_2'); ?></a>

									</div>
									<div class="delivery__contact">
										<div class="delivery__contact-label">Электронная почта</div>
										<a href="mailto:<?php echo esc_attr(my_opt('email')) ?>" class="delivery__contact-mail underLink"><?php the_opt('email'); ?></a>

									</div>

								</div>
							</div>

							<div class="article__right">
								<?php the_content(); ?>
							</div>
						</div>
					</div>

			</section>
			<section class="news">
				<div class="container">
					<div class="news-top top">
						<h3 class="h2">Другие статьи</h3>

					</div>
					<div class="news__wrapper">
						<?php get_template_part('template-parts/news-row') ?>

					</div>
				</div>


			</section>
		</div>
<?php endwhile;
endif ?>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>