<?php

namespace VG_Catalog\Domain\UseCases\Order\Actions;

use VG_Catalog\Domain\UseCases\Order\Dto\UpdateOrderDTO;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;
use VG_Catalog\Domain\UseCases\Order\OrderRepository;

class UpdateOrderAction
{
    public function __construct(
        protected OrderRepository $repository = new OrderRepository()
    ){}

    public function __invoke(UpdateOrderDTO $orderDTO): int
    {
        $order = $this->repository->getById($orderDTO->order_id);

        if (!$order) {
            throw new \InvalidArgumentException('Order not found', 404);
        }

        if ($orderDTO->status && in_array($orderDTO->status, OrderStatus::values()))
        {
            $order->update(['status' => OrderStatus::from($orderDTO->status)]);
        }

        return $order->id;
    }
}