<?php

namespace VG_Catalog\Domain\UseCases\Product\Actions;

use VG_Catalog\Domain\UseCases\Product\Dto\UpdateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Product;
use VG_Catalog\Domain\UseCases\Product\ProductRepository;

class UpdateProductAction
{
    public function __construct(
        protected ProductRepository $productRepository = new ProductRepository()
    ){}

    public function __invoke(UpdateProductDTO $productDTO): bool
    {
        $product = Product::where('post_id', $productDTO->product_id)->first();

        if (!$product) {
            throw new \InvalidArgumentException('Product not found', 404);
        }

        $post = get_post($product->post_id);
        $post->post_title = $productDTO->title;
        $postId = wp_insert_post($post->to_array());

        if ($product->mainImageId !== $productDTO->main_image) {
            delete_post_meta($postId, '_vg_main_image');
            if ($productDTO->main_image) {
                add_post_meta($postId, '_vg_main_image', $productDTO->main_image);
            }
        }
        if ($productDTO->gallery) {
            foreach ($productDTO->gallery as $key => $imageId) {
                if ($imageId !== $product->galleryImagesIds[$key]) {
                    delete_post_meta($postId, "_vg_gallery|vg_image|$key|0|value");
                    add_post_meta($postId, "_vg_gallery|vg_image|$key|0|value", $imageId);
                }
            }
        }
        else {
            foreach ($product->galleryImagesIds as $key => $imageId) {
                delete_post_meta($postId, "_vg_gallery|||$key|value");
                delete_post_meta($postId, "_vg_gallery|vg_image|$key|0|value");
            }
        }

        if ($productDTO->unit_id && get_post_meta($postId, '_unit|||0|id')->value !== $productDTO->unit_id) {
            $term = get_term($productDTO->unit_id, 'vg_product_unit');
            if ($term) {
                delete_post_meta($postId, '_unit|||0|value');
                delete_post_meta($postId, '_unit|||0|type');
                delete_post_meta($postId, '_unit|||0|subtype');
                delete_post_meta($postId, '_unit|||0|id');
                add_post_meta($postId, '_unit|||0|value', 'term:vg_product_unit:' . $term->term_id);
                add_post_meta($postId, '_unit|||0|type', 'term');
                add_post_meta($postId, '_unit|||0|subtype', 'vg_product_unit');
                add_post_meta($postId, '_unit|||0|id', $term->term_id);
            }
        }

        wp_delete_object_term_relationships($postId, 'vg_product_category');
        wp_set_object_terms($postId, $productDTO->categories, 'vg_product_category');

        return $this->productRepository->update($product, array_filter($productDTO->toArray()));
    }
}