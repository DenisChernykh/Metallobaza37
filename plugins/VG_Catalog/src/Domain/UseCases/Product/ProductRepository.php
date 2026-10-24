<?php

namespace VG_Catalog\Domain\UseCases\Product;

use VG_Catalog\Domain\UseCases\Product\Dto\CreateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\FiltersProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\UpdateProductDTO;

class ProductRepository
{
    public function create(array $product): Product
    {
        return Product::create($product);
    }

    public function getById($id): Product|null
    {
        return Product::with('groups.group.options')->find($id);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}