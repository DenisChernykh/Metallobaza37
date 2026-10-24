<?php

namespace VG_Catalog\Core;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use VG_Catalog\Core\Datastore\DatastoreTable;

class Fields
{
    public static function register()
    {
        add_action('carbon_fields_register_fields', [self::class, 'registerVgProductFields']);
        add_action('carbon_fields_register_fields', [self::class, 'registerVgCategoryFields']);
        add_action('carbon_fields_register_fields', [self::class, 'registerVgGroupOptionsFields']);
        add_action('carbon_fields_register_fields', [self::class, 'registerVgOptionsFields']);
    }

    public static function registerVgProductFields()
    {
        Container::make('post_meta', 'Дополнительные данные товара')
            ->where('post_type', '=', 'vg_product')
            ->add_fields([
                Field::make('text', 'description', 'Описание')
                    ->set_datastore(new DatastoreTable('wp_vg_products')),
                Field::make('text', 'price', 'Цена')
                    ->set_required()
                    ->set_datastore(new DatastoreTable('wp_vg_products')),
                Field::make('text', 'price_sale', 'Цена со скидкой')
                    ->set_datastore(new DatastoreTable('wp_vg_products')),
                Field::make('text', 'sku', 'Артикул')
                    ->set_required()
                    ->set_datastore(new DatastoreTable('wp_vg_products')),
                Field::make('association', 'unit', 'Ед. измерения')
                    ->set_types([
                        [
                            'type' => 'term',
                            'taxonomy' => 'vg_product_unit',
                        ]
                    ])
                    ->set_max(1),
                Field::make('association', 'group_id', 'Группа опций')
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'vg_group_options',
                        ]
                    ])
                    ->set_datastore(new DatastoreTable('wp_vg_product_groups')),
                Field::make('select', 'status', 'Статус')
                    ->add_options([
                        'in_stock' => 'В наличии',
                        'out_of_stock' => 'Отсутствует',
                    ]),
                Field::make('checkbox', 'is_hit', 'Хит продаж')
                    ->set_option_value('yes')
                    ->set_default_value(false),
                Field::make('image', 'vg_main_image', 'Главная картинка'),
                Field::make('complex', 'vg_gallery', 'Галерея')
                    ->add_fields([
                        Field::make('image', 'vg_image', 'Картинка')
                    ])
            ]);
    }

    public static function registerVgCategoryFields()
    {
        Container::make('term_meta', 'Дополнительно для категории')
            ->where('term_taxonomy', '=', 'vg_product_category')
            ->add_fields([
                Field::make('text', 'vg_category_weight', 'Вес категории')
                    ->set_default_value(0),
                Field::make('image', 'vg_category_image', 'Картинка категории')
            ]);
    }

    public static function registerVgGroupOptionsFields()
    {
        Container::make('post_meta', 'Настройки группы опций')
            ->where('post_type', '=', 'vg_group_options')
            ->add_fields([
                Field::make('checkbox', 'required', 'Обязательная группа')
                    ->set_option_value('yes')
                    ->set_default_value(true)
                    ->set_datastore(new DatastoreTable('wp_vg_group_options')),

                Field::make('checkbox', 'multiple', 'Множественный выбор')
                    ->set_option_value('yes')
                    ->set_default_value(true)
                    ->set_datastore(new DatastoreTable('wp_vg_group_options')),
            ]);
    }

    public static function registerVgOptionsFields()
    {
        Container::make('post_meta', 'Настройки опции')
            ->where('post_type', '=', 'vg_options')
            ->add_fields([
                Field::make('association', 'group_id', 'Группа опций')
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'vg_group_options',
                        ]
                    ])
                    ->set_max(1)
                    ->set_required(true)
                    ->set_datastore(new DatastoreTable('wp_vg_options')),
                Field::make('complex', 'vg_option_additional', 'Дополнительные параметры')
                    ->add_fields([
                        Field::make('text', 'vg_option_additional_title', 'Наименование параметра'),
                        Field::make('text', 'vg_option_additional_value', 'Значение параметра')
                    ])->set_datastore(new DatastoreTable('wp_vg_options')),
            ]);
    }
}
