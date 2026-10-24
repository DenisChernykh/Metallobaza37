<?php
// functions.php (самый верх)
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
	require $autoload;
} else {
	// На время разработки — чтобы не ловить белый экран, если vendor/ ещё не установлен
	error_log('Composer autoload not found: ' . $autoload);
}

use Kucrut\Vite;

add_action('wp_enqueue_scripts', function (): void {
	wp_enqueue_script_module('@wordpress/interactivity'); // ← правильный handle
}, 5);
add_action('wp_enqueue_scripts', function (): void {
	wp_register_script_module('@app/iapi-bridge', get_template_directory_uri() . '/assets/iapi-bridge.module.js', ['@wordpress/interactivity']);
	wp_enqueue_script_module('@app/iapi-bridge');
}, 9);
add_action('wp_enqueue_scripts', function (): void {

	// ДОЛЖНО соответствовать outDir на ДИСКЕ:
	$dist_path = get_template_directory() . '/assets/build';

	// Второй аргумент — ИСХОДНЫЙ путь к entry (как в vite.config.js -> input)
	Vite\enqueue_asset(
		$dist_path,
		'assets/js/src/main.ts',
		[
			'handle'    => 'theme-main',
			'in-footer' => true,
		]
	);
}, 10);

add_action('wp_enqueue_scripts', function (): void {
	$dist_path = get_template_directory() . '/assets/build';
	Vite\enqueue_asset(
		$dist_path,
		'assets/js/src/modules/cartv3/store/cart.store.ts',
		[
			'handle'    => 'cart-store',
			'in-footer' => true,
		]
	);
}, 12);
/**
 * Авто-preload всех .woff2-шрифтов из Vite-билда.
 */

function theme_preload_vite_fonts(): void
{
	$dist_path = get_template_directory() . '/assets/build';
	$fonts_dir = $dist_path . '/fonts';
	if (! is_dir($fonts_dir)) {
		return;
	}
	$fonts_url = get_template_directory_uri() . '/assets/build/fonts';
	$font_files = glob($fonts_dir . '/*.woff2');
	if (empty($font_files)) {
		return;
	}
	foreach ($font_files as $font_file) {
		$basename = basename($font_file);

		printf(
			"<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n",
			esc_url($fonts_url . '/' . $basename)
		);
	}
}


add_action('wp_head', 'theme_preload_vite_fonts', 5);
