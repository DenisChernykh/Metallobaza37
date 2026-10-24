<?php

namespace VG_Catalog\Domain\UseCases\Order;

class OrderRepository
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function getById(int $id)
    {
        return Order::find($id);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}