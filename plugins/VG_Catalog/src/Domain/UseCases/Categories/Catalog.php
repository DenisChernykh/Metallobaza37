<?php
namespace VG_Catalog\Domain\UseCases\Categories;

class Catalog {
    /**
     * Получить категории
     */
    public static function get_categories(int $parent_id = 0): array {
        $terms = get_terms([
            'taxonomy'   => 'vg_product_category',
            'parent'     => $parent_id,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        return array_map(function($term) {
            $image_id  = carbon_get_term_meta($term->term_id, 'vg_category_image');
            $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

            return [
                'id'    => $term->term_id,
                'name'  => $term->name,
                'link'  => get_term_link($term),
                'image' => $image_url,
            ];
        }, $terms);
    }

    /**
     * Получить товары категории
     */
    public static function get_products_by_category(int $cat_id, bool $include_subs = false): array {
        if (!$cat_id) return [];

        $term_ids = [$cat_id];

        if ($include_subs) {
            $children = get_term_children($cat_id, 'vg_product_category');
            if (!is_wp_error($children)) {
                $term_ids = array_merge($term_ids, $children);
            }
        }

        $query = new \WP_Query([
            'post_type'      => 'vg_product',
            'posts_per_page' => -1,
            'tax_query'      => [[
                'taxonomy' => 'vg_product_category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ]],
        ]);

        $result = [];
        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();

            $result[] = [
                'id'         => $id,
                'title'      => get_the_title(),
                'link'       => get_permalink(),
                'price'      => carbon_get_post_meta($id, 'vg_price'),
                'price_sale' => carbon_get_post_meta($id, 'vg_price_sale'),
                'image'      => get_the_post_thumbnail_url($id, 'medium'),
            ];
        }
        wp_reset_postdata();

        return $result;
    }
}
