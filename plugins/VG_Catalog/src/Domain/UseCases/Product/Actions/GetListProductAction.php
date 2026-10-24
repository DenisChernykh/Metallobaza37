<?php

namespace VG_Catalog\Domain\UseCases\Product\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Product\Dto\FiltersProductDTO;
use VG_Catalog\Domain\UseCases\Product\Product;

class GetListProductAction
{

    public function __invoke(FiltersProductDTO $filters): array|LengthAwarePaginator
    {
        $products = Product::query()->with([
            'groups.group.options',
            'categoriesRelation'
        ]);

        $queryFilters = [];
        if ($filters->title) {
            $queryFilters[] = [
                'field' => 'title',
                'operation' => 'like',
                'value' => $filters->title
            ];
        }

        if ($filters->price_min) {
            $queryFilters[] = [
                'field' => 'price',
                'operation' => 'gte',
                'value' => $filters->price_min
            ];
        }

        if ($filters->price_max) {
            $queryFilters[] = [
                'field' => 'price',
                'operation' => 'lte',
                'value' => $filters->price_max
            ];
        }

        applyQueryFilters($products, $queryFilters);

        if ($filters->category_id) {
            $products->whereHas('categoriesRelation', function ($query) use ($filters) {
                $query->where('term_taxonomy_id', $filters->category_id);
            });
        }
        $products->orderBy($filters->sort_by, $filters->sort_type);
        if ($filters->limit === 0) {
            return $products->get()->toArray();
        }
        return $products->paginate($filters->limit, page: $filters->page);
    }
}