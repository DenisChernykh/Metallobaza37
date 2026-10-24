<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Categories\Actions\CreateCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\DeleteCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetCategoryByIdAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetListCategoriesAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\UpdateCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Dto\CategoryIdDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\CreateCategoryDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\FilterCategoriesDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\UpdateCategoryDTO;

class CategoryService
{

    public function create(CreateCategoryDTO $categoryDTO, CreateCategoryAction $action): int
    {
        return $action($categoryDTO);
    }

    public function getById(CategoryIdDTO $categoryDTO, GetCategoryByIdAction $action): array|\InvalidArgumentException
    {
        return $action($categoryDTO);
    }

    public function getList(FilterCategoriesDTO $filterDTO, GetListCategoriesAction $action): array|LengthAwarePaginator
    {
        return $action($filterDTO);
    }

    public function update(UpdateCategoryDTO $categoryDTO, UpdateCategoryAction $action): bool|array|\InvalidArgumentException
    {
        return $action($categoryDTO);
    }

    public function delete(CategoryIdDTO $categoryDTO, DeleteCategoryAction $action): bool|\InvalidArgumentException
    {
        return $action($categoryDTO);
    }
}