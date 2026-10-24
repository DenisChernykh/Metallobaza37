<?php

namespace VG_Catalog\Domain\UseCases\Product\Actions;

use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Product;
use VG_Catalog\Domain\UseCases\Product\ProductRepository;

class DeleteProductAction
{
    public function __construct(
        protected ProductRepository $productRepository = new ProductRepository()
    ){}

    public function __invoke(ProductIdDTO $productIdDTO): bool
    {
        $product = Product::where('post_id', $productIdDTO->product_id)->first();
        if (!$product) {
            throw new \InvalidArgumentException('Product not found', 404);
        }
        wp_delete_post($product->post_id, true);
        return $this->productRepository->delete($product);
    }
}