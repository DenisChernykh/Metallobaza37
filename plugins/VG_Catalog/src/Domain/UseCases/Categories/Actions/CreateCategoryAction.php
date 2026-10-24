<?php

namespace VG_Catalog\Domain\UseCases\Categories\Actions;

use VG_Catalog\Domain\UseCases\Categories\Dto\CreateCategoryDTO;

class CreateCategoryAction
{

    public function __construct(

    ){}

    public function __invoke(CreateCategoryDTO $categoryDTO): int
    {
        $termId = wp_insert_term($categoryDTO->name, 'vg_product_category', [
            'slug' => $categoryDTO->slug,
            'description' => $categoryDTO->description,
            'parent' => $categoryDTO->parent_id
        ]);
        add_term_meta($termId['term_id'], '_vg_category_weight', $categoryDTO->weight ?? 0);
        if ($categoryDTO->image) {
            add_term_meta($termId['term_id'], '_vg_category_image', $categoryDTO->image);
        }

        return $termId['term_id'];
    }

}