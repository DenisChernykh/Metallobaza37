<?php
require_once get_template_directory() . '/vendor/autoload.php';

use Carbon_Fields\Container;
use Carbon_Fields\Field;

function metallobaza_boot_carbon_fields()
{
    \Carbon_Fields\Carbon_Fields::boot();
}
function metallobaza_register_theme_options()
{
    Container::make('theme_options', __('Настройки сайта', 'textdomain'))
        ->set_icon('dashicons-admin-generic')
        ->add_fields([
            Field::make('image', 'site_logo_header', 'Логотип в шапке')->set_width(50),
            Field::make('image', 'site_logo_footer', 'Логотип внизу сайта')->set_width(50),
            Field::make('text', 'phone_1', 'Первый телефон')->set_width(33),
            Field::make('text', 'phone_2', 'Второй телефон')->set_width(33),
            Field::make('text', 'email', 'Электронная почта')->set_width(33),
            Field::make('textarea', 'address', 'Адрес')->set_width(50),
            Field::make('textarea', 'work_time', 'Режим работы')->set_width(50),
            Field::make('file', 'price_list_google_url', 'Прайс-лист')
                ->set_help_text('Загрузите файл прайс-листа (PDF/XLSX).')
                ->set_value_type('id')
                ->set_width(100),
            Field::make('separator', 'sep_intro', 'Интро / Баннеры'),
            Field::make('complex', 'intro_slides', 'Слайды интро')
                ->set_layout('tabbed-vertical')
                ->set_help_text('Каждый слайд: фон, заголовок, описание. Бейдж опционально.')
                ->add_fields([
                    Field::make('image', 'bg', 'Фон слайда')
                        ->set_value_type('id')
                        ->set_width(50),

                    Field::make('text', 'title', 'Заголовок')
                        ->set_width(70),


                    Field::make('textarea', 'text', 'Описание')
                        ->set_rows(3),


                    Field::make('checkbox', 'has_badge', 'Показывать бейдж')
                        ->set_width(100),

                    Field::make('text', 'badge_text', 'Бейдж: текст')
                        ->set_conditional_logic([
                            ['field' => 'has_badge', 'value' => true],
                        ])
                        ->set_width(60),

                    Field::make('image', 'badge_icon_png', 'Бейдж: иконка')
                        ->set_value_type('id')
                        ->set_help_text('Загрузите Иконку. Рекомендация: 48×48 или 64×64, квадрат.')
                        ->set_conditional_logic([
                            ['field' => 'has_badge', 'value' => true],
                        ])
                        ->set_width(40),
                ]),
        ]);
}
function metallobaza_google_sheets_to_pdf_export_url(string $url): string
{
    $url = trim($url);
    if ($url === '') return '';

    $parts = wp_parse_url($url);
    if (empty($parts['host']) || $parts['host'] !== 'docs.google.com') {
        return $url;
    }

    $path = $parts['path'] ?? '';

    if (!preg_match('~^/spreadsheets/d/([^/]+)/~', $path, $m)) {
        return $url;
    }
    $file_id = $m[1];


    $gid = '';


    if (!empty($parts['query'])) {
        parse_str($parts['query'], $q);
        if (!empty($q['gid'])) $gid = (string) $q['gid'];
    }


    if (!$gid && !empty($parts['fragment'])) {
        if (preg_match('~gid=([0-9]+)~', $parts['fragment'], $fm)) {
            $gid = $fm[1];
        }
    }

    $export = "https://docs.google.com/spreadsheets/d/{$file_id}/export?format=pdf";
    if ($gid !== '') {
        $export .= "&gid=" . rawurlencode($gid);
    }

    return $export;
}
function metallobaza_register_carbon()
{
    Container::make('post_meta', __('Доставка', 'textdomain'))
        ->where('post_template', '=', 'pages/delivery-page.php')

        ->add_fields([
            Field::make('text', 'section_title', 'Заголовок секции'),
            Field::make('complex', 'delivery_groups', 'Группы доставки')
                ->set_width(50)
                ->set_layout('tabbed-vertical')
                ->add_fields([
                    Field::make('text', 'group_title', 'Заголовок группы')
                        ->set_help_text('Например: «6-ти метровый кузов»'),


                    Field::make('complex', 'group_rows', 'Строки в группе')
                        ->set_layout('tabbed-vertical')
                        ->set_help_text('Например: «6-ти метровый кузов»')
                        ->add_fields([
                            Field::make('text', 'row_desc', 'Описание'),
                            Field::make('text', 'row_price', 'Цена'),
                        ])
                ]),
            Field::make('complex', 'pay_groups', 'Способы оплаты')
                ->set_width(50)
                ->set_layout('tabbed-vertical')
                ->add_fields([
                    Field::make('text', 'pay_method', 'Метод оплаты')
                ])

        ]);
    Container::make('post_meta', 'Реквизиты компании')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'pages/contacts-page.php')
        ->add_fields([
            Field::make('separator', 'sep_requisites', 'Реквизиты'),
            Field::make('text', 'org_address', 'Фактический адрес')->set_width(50),
            Field::make('text', 'org_ogrn', 'ОГРН')->set_width(25),
            Field::make('text', 'org_inn', 'ИНН/КПП')->set_width(25),
            Field::make('text', 'org_okpo', 'ОКПО')->set_width(25),

            Field::make('text', 'org_okato', 'ОКАТО')->set_width(25),
            Field::make('text', 'org_okved', 'ОКВЭД')->set_width(25),

            Field::make('text', 'org_bank_name', 'Наименование банка')->set_width(50),
            Field::make('text', 'org_bik', 'БИК')->set_width(50),

            Field::make('text', 'org_rs', 'Расчетный счет')->set_width(50)
                ->set_help_text('Можно вводить с пробелами для удобства'),
            Field::make('text', 'org_ks', 'Корсчет')->set_width(50),

            Field::make('text', 'org_ceo', 'ФИО директора')->set_width(100),
        ]);
}

add_action('after_setup_theme', 'metallobaza_boot_carbon_fields');
add_action('carbon_fields_register_fields', 'metallobaza_register_theme_options');
add_action('carbon_fields_register_fields', 'metallobaza_register_carbon');