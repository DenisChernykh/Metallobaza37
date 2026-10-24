<?php

namespace VG_Catalog\Domain\UseCases\Product\Actions;

use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use VG_Catalog\Domain\UseCases\Product\Dto\CreateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Product;
use VG_Catalog\Domain\UseCases\Product\ProductGroup;
use VG_Catalog\Domain\UseCases\Product\ProductRepository;

class CreateProductAction
{
    public function __construct(
        protected ProductRepository $productRepository = new ProductRepository()
    ){}

    public function __invoke(CreateProductDTO $productDTO): Product
    {
        $postId = wp_insert_post([
            'post_title' => $productDTO->title,
            'post_status' => $productDTO->status ?? 'publish',
            'post_type' => 'vg_product',
        ]);

        if ($productDTO->main_image) {
            add_post_meta($postId, '_vg_main_image', $productDTO->main_image);
        }
        if ($productDTO->gallery) {
            foreach ($productDTO->gallery as $key => $imageId) {
                add_post_meta($postId, "_vg_gallery|||$key|value", '_');
                add_post_meta($postId, "_vg_gallery|vg_image|$key|0|value", $imageId);
            }
        }
        if ($productDTO->unit_id) {
            $term = get_term($productDTO->unit_id, 'vg_product_unit');
            if ($term) {
                add_post_meta($postId, '_unit|||0|value', 'term:vg_product_unit:' . $term->term_id);
                add_post_meta($postId, '_unit|||0|type', 'term');
                add_post_meta($postId, '_unit|||0|subtype', 'vg_product_unit');
                add_post_meta($postId, '_unit|||0|id', $term->term_id);
            }
        }
        else {
            add_post_meta($postId, '_unit|||0|_empty', '');
        }
        wp_set_object_terms($postId, $productDTO->categories, 'vg_product_category');


        foreach ($productDTO->groups_options as $group) {
            $groupId = GroupOption::query()->find($group)->post_id;
            ProductGroup::create(['product_id' => $postId, 'group_id' => $groupId]);
        }
        $product = $this->productRepository->create(array_merge($productDTO->toArray(), ['post_id' => $postId]));
        return $product;
    }
}