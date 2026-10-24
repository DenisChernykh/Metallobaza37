<?php

namespace VG_Catalog\Helpers;

use VG_Catalog\Domain\UseCases\Product\Product;

class CategoryHelper
{
    public function getProductsByCategoryId(
        ?int $categoryId = null,
        bool $deep = true,
        int $limit = 15,
        array $excludeIds = [],
        bool $random = false,
        bool $hasSale = false,
    )
    {
        $products = Product::query()->with('categoriesRelation');

        if ($categoryId !== null) {
            $categories[] = $categoryId;
            if ($deep) {
                $parent = get_term($categoryId, 'vg_product_category')->parent;
                while ($parent !== 0) {
                    $categories[] = $parent;
                    $parent = get_term($parent, 'vg_product_category')->parent;
                }
            }
            $products->whereHas('categoriesRelation', function ($query) use ($categories) {
                $query->whereIn('term_taxonomy_id', $categories);
            });
        }

        if (!empty($excludeIds)) {
            $products->whereNotIn('post_id', $excludeIds);
        }

        if ($hasSale) {
            $products->where('sale_price', '>', 0);
        }

        if ($random) {
            $products->inRandomOrder();
        }

        if ($limit > 0) {
            $products->limit($limit);
        }

        return $products->get()->each->setHidden(['categoriesFilter']);
    }
}