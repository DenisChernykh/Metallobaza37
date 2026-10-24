<?php

namespace VG_Catalog\Domain\UseCases\Order\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Order\Dto\FilterOrdersDTO;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;
use VG_Catalog\Domain\UseCases\Order\Order;

class GetListOrdersAction
{
    public function __invoke(FilterOrdersDTO $filters): array|LengthAwarePaginator
    {
        $orders = Order::query()->with(['items.product', 'items.options.option']);
        $queryFilters = [];
        if ($filters->status && in_array($filters->status, OrderStatus::values())) {
            $queryFilters[] = [
                'field' => 'status',
                'operation' => 'qe',
                'value' => OrderStatus::from($filters->status),
            ];
        }

        applyQueryFilters($orders, $queryFilters);
        if ($filters->limit === 0) {
            return $orders->get()->toArray();
        }
        return $orders->paginate($filters->limit, page: $filters->page);
    }
}