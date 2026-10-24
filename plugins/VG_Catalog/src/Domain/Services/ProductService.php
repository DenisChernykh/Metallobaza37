<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Product\Actions\CreateProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\DeleteProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\GetListProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\GetProductByIdAction;
use VG_Catalog\Domain\UseCases\Product\Actions\UpdateProductAction;
use VG_Catalog\Domain\UseCases\Product\Dto\CreateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\FiltersProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\UpdateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Product;

class ProductService
{

    public function create(CreateProductDTO $productDTO, CreateProductAction $action): Product
    {
        return $action($productDTO);
    }

    public function getById(ProductIdDTO $productIdDTO, GetProductByIdAction $action): Product|\InvalidArgumentException
    {
        return $action($productIdDTO);
    }

    public function getList(FiltersProductDTO $filtersDTO, GetListProductAction $action): array|LengthAwarePaginator
    {
        return $action($filtersDTO);
    }

    public function update(UpdateProductDTO $productDTO, UpdateProductAction $action): bool
    {
        return $action($productDTO);
    }

    public function delete(ProductIdDTO $productIdDTO, DeleteProductAction $action): bool
    {
        return $action($productIdDTO);
    }
}