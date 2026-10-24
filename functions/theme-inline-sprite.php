<?php function theme_inline_sprite()
{
	$path = get_stylesheet_directory() . '/assets/svg/sprite.svg';
	if (file_exists($path)) {
		echo '<div aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">';
		echo file_get_contents($path);
		echo '</div>';
	}
}
add_action('wp_footer', 'theme_inline_sprite', 5);

function icon(string $name, ?string $class = '', ?int $size = null, string $prefix = 'i-'): string
{
	$name = preg_replace('/[^a-z0-9\-_]/i', '', $name);
	if ($name === '') return '';

	// всегда есть класс "icon"
	$classes = trim('icon ' . $class);
	$attr = ' class="' . esc_attr($classes) . '"';

	if ($size && $size > 0) {
		$attr .= ' width="' . (int)$size . '" height="' . (int)$size . '"';
	}

	return '<svg' . $attr . '><use href="#' . esc_attr($prefix . $name) . '"></use></svg>';
}

function the_icon(string $name, ?string $class = '', ?int $size = null, string $prefix = 'i-'): void
{
	echo icon($name, $class, $size, $prefix);
}
