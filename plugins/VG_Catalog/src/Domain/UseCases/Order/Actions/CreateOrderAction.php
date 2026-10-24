<?php

namespace VG_Catalog\Domain\UseCases\Order\Actions;

use VG_Catalog\Application\Storage\CookieStorage;
use VG_Catalog\Domain\UseCases\Cart\Cart;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Order\Dto\CreateOrderDTO;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;
use VG_Catalog\Domain\UseCases\Order\OrderItem;
use VG_Catalog\Domain\UseCases\Order\OrderItemOption;
use VG_Catalog\Domain\UseCases\Order\OrderRepository;

class CreateOrderAction
{
    public function __construct(
        protected OrderRepository $repository = new OrderRepository(),
        protected CookieStorage $storage = new CookieStorage('vg_cart')
    ){}

    public function __invoke(CreateOrderDTO $orderDTO): int
    {
        $userId = get_current_user_id() === 0 ? null : get_current_user_id();
        $order = $this->repository->create([
            'name' => $orderDTO->name,
            'phone' => $orderDTO->phone,
            'additional' => json_encode($orderDTO->additional, JSON_UNESCAPED_UNICODE),
            'status' => OrderStatus::PROGRESS,
            'user_id' => $userId
        ]);
        $orderItemsData = [];
        $cartIdsToDelete = [];
        $orderOptionsData = [];
        if ($userId) {
            $cart = $orderDTO->cart_ids ? Cart::find($orderDTO->cart_ids) : Cart::where('user_id', get_current_user_id())->get();

            foreach ($cart as $item) {
                $orderItemsData[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'order_id' => $order->id
                ];
                $cartIdsToDelete[] = $item->id;
            }

            $orderItems = $order->items()->createMany($orderItemsData);

            foreach ($orderItems as $index => $orderItem) {
                $item = $cart[$index];
                foreach ($item->options as $option) {
                    $orderOptionsData[] = [
                        'order_item_id' => $orderItem->id,
                        'option_id' => $option->option_id,
                    ];
                }
            }

            if ($orderOptionsData) {
                OrderItemOption::query()->insert($orderOptionsData);
            }

            Cart::whereIn('id', $cartIdsToDelete)->delete();
        }
        else {
            $cart = $this->storage->get();
            foreach ($cart as $cartId => $item) {
                if (in_array($cartId, $orderDTO->cart_ids) || !$orderDTO->cart_ids) {
                    $orderItemsData[$cartId] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'order_id' => $order->id
                    ];

                    $cartIdsToDelete[] = $cartId;
                }
            }

            $orderItems = $order->items()->createMany($orderItemsData);

            foreach ($orderItems as $itemId => $orderItem) {
                $cartId = $cartIdsToDelete[$itemId];
                $item = $cart[$cartId];
                foreach ($item['option_ids'] as $option) {
                    $orderOptionsData[] = [
                        'order_item_id' => $orderItem->id,
                        'option_id' => $option,
                    ];
                }
            }
            if ($orderOptionsData) {
                OrderItemOption::query()->insert($orderOptionsData);
            }

            foreach ($cartIdsToDelete as $cartId) {
                unset($cart[$cartId]);
            }
            $this->storage->set($cart);
        }
        return $order->id;
    }
}