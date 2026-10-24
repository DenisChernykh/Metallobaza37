<?php

namespace VG_Catalog\Core;

class PostTypes
{
    public static function register()
    {
        add_action('init', [self::class, 'registerVgProductCpt']);
        add_action('init', [self::class, 'registerVgProductCategoryTax']);
        add_action('init', [self::class, 'registerVgProductUnit']);
        add_action('init', [self::class, 'registerVgGroupOptions']);
        add_action('init', [self::class, 'registerVgOptions']);
    }

    public static function registerVgProductCpt()
    {
        $labels = [
            'name' => 'VG Товары',
            'singular_name' => 'VG Товар',
            'add_new' => 'Добавить товар',
            'add_new_item' => 'Добавить товар',
            'edit_item' => 'Редактировать товар',
            'new_item' => 'Новый товар',
            'view_item' => 'Просмотр товара',
            'search_items' => 'Искать товары',
            'not_found' => 'Товары не найдены',
            'not_found_in_trash' => 'В корзине товары не найдены',
            'all_items' => 'Все товары',
            'menu_name' => 'VG Товары',
            'name_admin_bar' => 'VG Товар'
        ];

        register_post_type('vg_product', [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-cart',
            'menu_position' => 5,
            'supports' => ['title', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'product',
                'with_front' => false
            ],
            'show_in_rest' => true
        ]);
    }

    public static function registerVgProductCategoryTax()
    {
        register_taxonomy('vg_product_category', 'vg_product', [
            'label' => 'Категории',
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'public' => true,
            'publicly_queryable' => true,
            'rewrite' => [
                'slug' => 'katalog',
                'with_front' => false,
                'hierarchical' => true
            ]
        ]);
    }

    public static function registerVgProductUnit()
    {
        $labels = [
            'name' => 'Единицы измерения',
            'singular_name' => 'Единица измерения',
            'add_new' => 'Добавить единицу измерения',
            'add_new_item' => 'Добавить единицу измерения',
            'edit_item' => 'Редактировать единицу измерения',
            'new_item' => 'Новая единица измерения',
            'view_item' => 'Просмотр единицы измерения',
            'search_items' => 'Искать единицу измерения',
            'not_found' => 'Единицы измерения не найдены',
            'not_found_in_trash' => 'В корзине единицы измерения не найдены',
            'all_items' => 'Все единицы измерения',
            'menu_name' => 'Единицы измерения',
            'name_admin_bar' => 'Единицы измерения'
        ];

        register_taxonomy('vg_product_unit', 'vg_product', [
            'label' => 'Единицы измерения',
            'labels' => $labels,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'show_in_rest' => true
        ]);
    }

    public static function registerVgGroupOptions()
    {
        $labels = [
            'name' => 'VG Группы опций',
            'singular_name' => 'VG Группа опций',
            'add_new' => 'Добавить группу',
            'add_new_item' => 'Добавить новую группу опций',
            'edit_item' => 'Редактировать группу опций',
            'new_item' => 'Новая группа опций',
            'view_item' => 'Просмотреть группу опций',
            'search_items' => 'Найти группу опций',
            'not_found' => 'Групп опций не найдено',
            'not_found_in_trash' => 'В корзине групп опций не найдено'
        ];
        register_post_type('vg_group_options', [
            'label' => 'VG Группы опций',
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'capability_type' => 'post',
            'supports' => ['title']
        ]);
    }

    public static function registerVgOptions()
    {
        $labels = [
            'name' => 'VG опции',
            'singular_name' => 'VG опции',
            'add_new' => 'Добавить опцию',
            'add_new_item' => 'Добавить новую опцию',
            'edit_item' => 'Редактировать опцию',
            'new_item' => 'Новая опция',
            'view_item' => 'Просмотреть опции',
            'search_items' => 'Найти опции',
            'not_found' => 'Опции не найдены',
            'not_found_in_trash' => 'В корзине опции не найдены'
        ];
        register_post_type('vg_options', [
            'label' => 'VG Опции товаров',
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'supports' => ['title']
        ]);
    }
}
