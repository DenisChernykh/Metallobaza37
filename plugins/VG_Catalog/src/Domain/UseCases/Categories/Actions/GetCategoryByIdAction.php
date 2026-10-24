<?php

namespace VG_Catalog\Domain\UseCases\Categories\Actions;

use VG_Catalog\Domain\UseCases\Categories\Dto\CategoryIdDTO;

class GetCategoryByIdAction
{
    public function __invoke(CategoryIdDTO $categoryDTO)
    {
        $term = get_term($categoryDTO->category_id);

        if ($term === null) {
            throw new \InvalidArgumentException('Category not found', 404);
        }

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
        ];
    }
}