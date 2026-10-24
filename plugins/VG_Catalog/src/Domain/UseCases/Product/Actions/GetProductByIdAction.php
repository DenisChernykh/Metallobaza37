<?php

namespace VG_Catalog\Domain\UseCases\Product\Actions;

use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Product;
use VG_Catalog\Domain\UseCases\Product\ProductRepository;

class GetProductByIdAction
{

    public function __construct(
        protected ProductRepository $productRepository = new ProductRepository()
    ){}

    public function __invoke(ProductIdDTO $productIdDTO): Product|\InvalidArgumentException
    {
        $product = Product::where('post_id', $productIdDTO->product_id)->first();
        if (!$product) {
            throw new \InvalidArgumentException('Product not found', 404);
        }
        return $product;
    }
}