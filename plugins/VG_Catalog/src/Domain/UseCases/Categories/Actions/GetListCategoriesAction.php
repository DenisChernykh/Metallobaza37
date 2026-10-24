<?php

namespace VG_Catalog\Domain\UseCases\Categories\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Categories\Dto\FilterCategoriesDTO;

class GetListCategoriesAction
{
    public function __invoke(FilterCategoriesDTO $filterCategoriesDTO): array|LengthAwarePaginator
    {
        $args = [
            'taxonomy'   => 'vg_product_category',
            'hide_empty' => false,
            'meta_key'   => '_vg_category_weight',
            'orderby'    => '_vg_category_weight',
        ];
        if ($filterCategoriesDTO->parent_id !== null) {
            $args['parent'] = $filterCategoriesDTO->parent_id;
        }

        $terms = get_terms($args);

        if (is_wp_error($terms)) {
            return [];
        }

        $terms = array_map(function($term) {
            $image_id  = carbon_get_term_meta($term->term_id, 'vg_category_image');
            $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

            return [
                'id'    => $term->term_id,
                'name'  => $term->name,
                'slug'  => $term->slug,
                'description'  => $term->description,
                'parent'  => $term->parent,
                'link'  => get_term_link($term),
                'image' => $image_url,
                'weight'  => get_term_meta($term->term_id, '_vg_category_weight', true),
            ];
        }, $terms);

        if ($filterCategoriesDTO->limit === 0) {
            return $terms;
        }
        return paginateArray($terms, $filterCategoriesDTO->limit, $filterCategoriesDTO->page);
    }
}