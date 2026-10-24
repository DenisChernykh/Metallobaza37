<?php
add_action('template_redirect', function () {
	if (is_category('news')) {
		wp_redirect(home_url('/news/'), 301);
		exit;
	}
});
add_filter('wpseo_breadcrumb_links', 'add_news_page_to_breadcrumbs');

function add_news_page_to_breadcrumbs($links)
{
	if (is_single() && in_category('news')) {


		$news_page = get_page_by_path('news');
		if (!$news_page) return $links;


		$news_link = [
			'url'  => get_permalink($news_page),
			'text' => 'Новости',
		];


		array_splice($links, 1, 0, [$news_link]);


		foreach ($links as $i => $link) {
			if (!empty($link['term']) && $link['term'] instanceof WP_Term && $link['term']->slug === 'news') {
				unset($links[$i]);
			}
		}

		$links = array_values($links);
	}

	return $links;
}

add_filter('wpseo_breadcrumb_links', function ($links) {
	if (is_search()) {

		array_pop($links);

		$links[] = [
			'url' => '',
			'text' => 'Поиск',
		];
	}
	return $links;
});
