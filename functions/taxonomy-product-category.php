<?php
if (!defined('ABSPATH')) exit;
function metallobaza_register_tax_product_category()
{
	register_taxonomy('product_category', ['product'], [
		'labels' => [
			'name'              => 'Категории товаров',
			'singular_name'     => 'Категория товара',
			'search_items'      => 'Найти категорию',
			'all_items'         => 'Все категории',
			'parent_item'       => 'Родительская категория',
			'parent_item_colon' => 'Родительская категория:',
			'edit_item'         => 'Редактировать категорию',
			'update_item'       => 'Обновить категорию',
			'add_new_item'      => 'Добавить категорию',
			'new_item_name'     => 'Название новой категории',
			'menu_name'         => 'Категории товаров',
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => ['slug' => 'product-category'],
	]);
}
add_action('init', 'metallobaza_register_tax_product_category');
