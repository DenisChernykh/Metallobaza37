<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\UseCases\Order\Actions\CreateOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\DeleteOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\GetByIdOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\GetListOrdersAction;
use VG_Catalog\Domain\UseCases\Order\Actions\UpdateOrderAction;
use VG_Catalog\Domain\UseCases\Order\Dto\CreateOrderDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\FilterOrdersDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\OrderIdDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\UpdateOrderDTO;

class OrderService
{

    public function create(CreateOrderDTO $orderDTO, CreateOrderAction $action): int
    {
        return $action($orderDTO);
    }

    public function getList(FilterOrdersDTO $filterDTO, GetListOrdersAction $action): array|LengthAwarePaginator
    {
        return $action($filterDTO);
    }

    public function getById(OrderIdDTO $orderDTO, GetByIdOrderAction $action): array
    {
        return $action($orderDTO);
    }

    public function update(UpdateOrderDTO $orderDTO, UpdateOrderAction $action): bool
    {
        return $action($orderDTO);
    }

    public function delete(OrderIdDTO $orderDTO, DeleteOrderAction $action): bool
    {
        return $action($orderDTO);
    }

}