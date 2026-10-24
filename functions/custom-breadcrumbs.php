<?php

add_filter('wpseo_breadcrumb_links', function($links) {
    if (is_singular('vg_product')) {
        global $post;

        // 1️⃣ Очищаем стандартные хлебные крошки Yoast
        $links = [];

        // 2️⃣ Добавляем "Главная"
        $links[] = [
            'url' => home_url('/'),
            'text' => 'Главная'
        ];

        // 3️⃣ Добавляем "Каталог"
        $links[] = [
            'url' => site_url('/katalog/'),
            'text' => 'Каталог'
        ];

        // 4️⃣ Получаем категории (таксономия для vg_product)
        $terms = wp_get_post_terms($post->ID, 'vg_product_category');

        if (!empty($terms) && !is_wp_error($terms)) {
            // Берём самую "глубокую" категорию (или первую — как нужно)
            $term = $terms[0];

            // 5️⃣ Добавляем иерархию категорий
            $ancestors = get_ancestors($term->term_id, 'vg_product_category');
            $ancestors = array_reverse($ancestors);

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'vg_product_category');
                $links[] = [
                    'url' => get_term_link($ancestor),
                    'text' => $ancestor->name
                ];
            }

            // Добавляем текущую категорию
            $links[] = [
                'url' => get_term_link($term),
                'text' => $term->name
            ];
        }

        // 6️⃣ Добавляем сам товар (без ссылки)
        $links[] = [
            'url' => '',
            'text' => get_the_title($post)
        ];
    }
    elseif (is_tax('vg_product_category')) {
        $term = get_queried_object();

        $links = [];

        // Главная
        $links[] = [
            'url' => home_url('/'),
            'text' => 'Главная'
        ];

        // Каталог
        $links[] = [
            'url' => site_url('/katalog/'),
            'text' => 'Каталог'
        ];

        // Родители категории (если есть)
        $ancestors = get_ancestors($term->term_id, 'vg_product_category');
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'vg_product_category');
            $links[] = [
                'url' => get_term_link($ancestor),
                'text' => $ancestor->name
            ];
        }

        // Текущая категория (без ссылки)
        $links[] = [
            'url' => '',
            'text' => $term->name
        ];
    }
    elseif(is_page('favorite'))
    {
        $links = [];

        // Главная
        $links[] = [
            'url' => home_url('/'),
            'text' => 'Главная'
        ];

        // Избранное
        $links[] = [
            'url' => '',
            'text' => 'Избранное'
        ];
    }
    elseif(is_page('katalog')){
        $links = [];

        // Главная
        $links[] = [
            'url' => home_url('/'),
            'text' => 'Главная'
        ];

        // Избранное
        $links[] = [
            'url' => '',
            'text' => 'Каталог'
        ];
    }
    elseif(is_page('cart')){
        $links = [];

        // Главная
        $links[] = [
            'url' => home_url('/'),
            'text' => 'Главная'
        ];

        // Избранное
        $links[] = [
            'url' => '',
            'text' => 'Корзина'
        ];
    }

    return $links;
});
