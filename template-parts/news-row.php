<?php
$exclude_id = is_singular() ? get_queried_object_id() : 0;
$args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
	'category_name'       => 'news',
	'orderby'             => 'date',
	'order'               => 'DESC',
];
if ($exclude_id) {
	$args['post__not_in'] = [$exclude_id];
}
$news = new WP_Query($args);
if ($news->have_posts()):
	$i = 0;

	while ($news->have_posts()):
		$news->the_post();
		$i++;
		$content = get_the_excerpt($post->ID);
		if ($i === 1): ?>

			<a href="<?php the_permalink() ?>" class="news-card news-card--large">
				<div class=" news-card__image">
					<?php if (has_post_thumbnail()):
						$alt = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ?: get_the_title();
						the_post_thumbnail('news-thumb', ['alt' => esc_attr($alt)]);
					else: ?>
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/fallback-news.webp'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					<?php endif; ?>
					<div class="news-card__blur">
					</div>
					<div class="news-card__link">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M2.42777 0.549925C2.15162 0.549929 1.92777 0.77379 1.92777 1.04993C1.92778 1.32607 2.15164 1.54993 2.42778 1.54993L2.42777 0.549925ZM10.9498 1.0498L11.4498 1.0498C11.4498 0.917195 11.3971 0.790017 11.3033 0.696249C11.2095 0.60248 11.0824 0.549803 10.9498 0.549805L10.9498 1.0498ZM10.4498 9.4506C10.4498 9.72674 10.6736 9.9506 10.9498 9.9506C11.2259 9.9506 11.4498 9.72674 11.4498 9.4506L10.4498 9.4506ZM0.696719 10.5957L0.343165 10.9493L1.05027 11.6564L1.40383 11.3029L0.696719 10.5957ZM2.42777 1.04993L2.42778 1.54993L10.9498 1.5498L10.9498 1.0498L10.9498 0.549805L2.42777 0.549925L2.42777 1.04993ZM10.9498 1.0498L10.4498 1.0498L10.4498 9.4506L10.9498 9.4506L11.4498 9.4506L11.4498 1.0498L10.9498 1.0498ZM10.9498 1.0498L10.5962 0.696251L0.696719 10.5957L1.05027 10.9493L1.40383 11.3029L11.3033 1.40336L10.9498 1.0498Z" fill="#fff" />
						</svg>
					</div>
					<time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="news-card__date"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
				</div>

				<div class="news-card__content">
					<h4 class="news-card__title h4"><?php echo esc_html(get_the_title()) ?></h4>
					<p class="news-card__text"><? echo $content ?></p>
				</div>

			</a>
		<?php else: ?>
			<a href="<?php the_permalink() ?>" class="news-card news-card--small">
				<div class=" news-card__image">
					<?php if (has_post_thumbnail()):
						$alt = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ?: get_the_title();
						the_post_thumbnail('news-thumb', ['alt' => esc_attr($alt)]);
					else: ?>
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/fallback-news.webp'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					<?php endif; ?>
					<div class="news-card__blur">
					</div>
					<div class="news-card__link">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M2.42777 0.549925C2.15162 0.549929 1.92777 0.77379 1.92777 1.04993C1.92778 1.32607 2.15164 1.54993 2.42778 1.54993L2.42777 0.549925ZM10.9498 1.0498L11.4498 1.0498C11.4498 0.917195 11.3971 0.790017 11.3033 0.696249C11.2095 0.60248 11.0824 0.549803 10.9498 0.549805L10.9498 1.0498ZM10.4498 9.4506C10.4498 9.72674 10.6736 9.9506 10.9498 9.9506C11.2259 9.9506 11.4498 9.72674 11.4498 9.4506L10.4498 9.4506ZM0.696719 10.5957L0.343165 10.9493L1.05027 11.6564L1.40383 11.3029L0.696719 10.5957ZM2.42777 1.04993L2.42778 1.54993L10.9498 1.5498L10.9498 1.0498L10.9498 0.549805L2.42777 0.549925L2.42777 1.04993ZM10.9498 1.0498L10.4498 1.0498L10.4498 9.4506L10.9498 9.4506L11.4498 9.4506L11.4498 1.0498L10.9498 1.0498ZM10.9498 1.0498L10.5962 0.696251L0.696719 10.5957L1.05027 10.9493L1.40383 11.3029L11.3033 1.40336L10.9498 1.0498Z" fill="#fff" />
						</svg>
					</div>
					<time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="news-card__date"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
				</div>
				<div class="news-card__content">
					<h4 class="news-card__title h4"><?php echo esc_html(get_the_title()) ?></h4>
					<p class="news-card__text"><? echo $content; ?></p>
				</div>

			</a>
		<?php endif ?>
	<?php endwhile;
	wp_reset_postdata();
endif ?>