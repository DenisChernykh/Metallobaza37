<?php

/**********************************************************/
/*	@copyright	VICTORY group				              */
/*	@support	https://victoryagency.ru				  */
/**********************************************************/

// отключаем админ бар
show_admin_bar(false);

// удаляем стили блоков WordPress
function remove_block_library_css()
{
	if (is_home() || is_archive() || is_category()) {
		wp_dequeue_style('wp-block-library');
	}
}
add_action('wp_enqueue_scripts', 'remove_block_library_css');

// функция проверки на подстраницу
function is_subpage()
{
	global $post;

	if (is_page() && $post->post_parent) {
		return $post->post_parent;
	}
	return false;
}

// откдючаем p и br в conctact-form
add_filter('wpcf7_autop_or_not', '__return_false');

/* allow svg mime type */
add_filter('upload_mimes', 'svg_upload_allow');

# Добавляет SVG в список разрешенных для загрузки файлов.
function svg_upload_allow($mimes)
{
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}
add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);
# Исправление MIME типа для SVG файлов.
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
{

	// WP 5.1 +
	if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
		$dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
	} else {
		$dosvg = ('.svg' === strtolower(substr($filename, -4)));
	}

	// mime тип был обнулен, поправим его
	// а также проверим право пользователя
	if ($dosvg) {

		// разрешим
		if (current_user_can('manage_options')) {

			$data['ext'] = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		// запретим
		else {
			$data['ext'] = false;
			$data['type'] = false;
		}
	}

	return $data;
}

// удаляем следы WordPress
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
function complete_version_removal()
{
	return '';
}
add_filter('the_generator', 'complete_version_removal');

//Удаление всех мета генераторов
function remove_meta_generators($html)
{
	$pattern = '/<meta name(.*)=(.*)"generator"(.*)>/i';
	$html = preg_replace($pattern, '', $html);
	return $html;
}
function clean_meta_generators($html)
{
	ob_start('remove_meta_generators');
}
add_action('get_header', 'clean_meta_generators', 100);
add_action('wp_footer', function () {
	ob_end_flush();
}, 100);
// delete all emojis
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('tiny_mce_plugins', 'disable_wp_emojis_in_tinymce');
function disable_wp_emojis_in_tinymce($plugins)
{
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	} else {
		return array();
	}
}

// убираем type="javascript" и type text/css
add_theme_support('html5', array('script', 'style'));
add_action('template_redirect', function () {
	ob_start(function ($buffer) {
		$buffer = str_replace(array(' type="text/javascript"', " type='text/javascript'"), '', $buffer);
		$buffer = str_replace(array(' type="text/css"', " type='text/css'"), '', $buffer);
		return $buffer;
	});
});

// удаление всех комментариях с кода
// function callback_clean_html($buffer)
// {
// 	$search = array(
// 		// '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
// 		// '/[^\S ]+\</s',     // strip whitespaces before tags, except space
// 		'/(\s)+/s',         // shorten multiple whitespace sequences
// 		'/<!--(.|\s)*?-->/' // Remove HTML comments
// 	);

// 	$replace = array(
// 		// '>',
// 		// '<',
// 		'\\1',
// 		''
// 	);

// 	$buffer = preg_replace($search, $replace, $buffer);

// 	return $buffer;
// }
// function buffer_start()
// {
// 	ob_start("callback_clean_html");
// }
// function buffer_end()
// {
// 	ob_end_flush();
// }
// add_action('get_header', 'buffer_start');
// add_action('wp_footer', 'buffer_end');

## Удаляет "Рубрика: ", "Метка: " и т.д. из заголовка архива
add_filter('get_the_archive_title', function ($title) {
	return preg_replace('~^[^:]+: ~', '', $title);
});
add_filter('get_the_archive_title_prefix', '__return_empty_string');

// удаляет H2 из шаблона пагинации
add_filter('navigation_markup_template', 'my_navigation_template', 10, 2);
function my_navigation_template($template, $class)
{
	/*
					  Вид базового шаблона:
					  <nav class="navigation %1$s" role="navigation">
						  <h2 class="screen-reader-text">%2$s</h2>
						  <div class="nav-links">%3$s</div>
					  </nav>
					  */

	return '
	<nav class="navigation %1$s" role="navigation">
		<div class="nav-links">%3$s</div>
	</nav>
	';
}

// добавляем классы к меню li и a
function add_additional_class_on_li($classes, $item, $args)
{
	if (isset($args->li_class)) {
		$classes[] = $args->li_class;
	}
	return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);
function add_specific_menu_location_atts($atts, $item, $args)
{
	if (isset($args->a_class)) {
		$atts['class'] = $args->a_class;
	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'add_specific_menu_location_atts', 10, 3);
add_action('after_setup_theme', function () {
	register_nav_menus([
		'topbar' => 'Верхнее меню',
	]);
});
add_action('init', function () {
	add_post_type_support('page', 'excerpt');
});