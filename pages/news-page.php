<?php
/**
 * Template Name: Новости
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
			<h2 class="h2">Новости</h2>
		</div>
		<div class="news-page__grid">

			<?php $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
			$query = new WP_Query([
				'post_type' => 'post',
				'posts_per_page' => 6,
				'category_name' => 'news',
				'paged'          => $paged,


			]); ?>

			<?php get_template_part('template-parts/news-card', null, ['query' => $query]); ?>


		</div>
		<?php get_template_part('template-parts/pagination', null, ['query' => $query, 'siblings' => 1]) ?>
	</div>
</section>

<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>