<?php

namespace VG_Catalog\Domain\UseCases\Order\Actions;

use VG_Catalog\Domain\UseCases\Order\Dto\OrderIdDTO;
use VG_Catalog\Domain\UseCases\Order\OrderRepository;

class GetByIdOrderAction
{
    public function __construct(
        protected OrderRepository $repository = new OrderRepository()
    ){}

    public function __invoke(OrderIdDTO $orderDTO)
    {
        $order = $this->repository->getById($orderDTO->order_id);

        if (!$order) {
            throw new \InvalidArgumentException('Order not found', 404);
        }

        return $order->with(['items.product', 'items.options.option'])->first()->toArray();
    }
}