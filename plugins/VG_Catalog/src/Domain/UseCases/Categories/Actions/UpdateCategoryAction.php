<?php

namespace VG_Catalog\Domain\UseCases\Categories\Actions;

use VG_Catalog\Domain\UseCases\Categories\Dto\UpdateCategoryDTO;

class UpdateCategoryAction
{
    public function __invoke(UpdateCategoryDTO $categoryDTO): bool|array
    {
        $term = get_term($categoryDTO->category_id);

        if ($term === null) {
            throw new \InvalidArgumentException('Category not found', 404);
        }

        if ($categoryDTO->image) {
            $termMeta = get_term_meta($term->term_id);
            update_term_meta($term->term_id, '_vg_category_image', $categoryDTO->image, $termMeta['_vg_category_image'][0]);
        }

        return wp_update_term($term->term_id, 'vg_product_category', array_filter($categoryDTO->toArray()));
    }
}