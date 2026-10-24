<?php

namespace VG_Catalog\Domain\UseCases\Categories\Actions;

use VG_Catalog\Domain\UseCases\Categories\Dto\CategoryIdDTO;

class DeleteCategoryAction
{
    public function __invoke(CategoryIdDTO $categoryDTO): bool
    {
        $term = get_term($categoryDTO->category_id);

        if ($term === null) {
            throw new \InvalidArgumentException('Category not found', 404);
        }
        return wp_delete_term($term->term_id, 'vg_product_category');
    }
}