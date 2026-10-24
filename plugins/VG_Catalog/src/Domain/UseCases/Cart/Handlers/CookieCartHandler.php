<?php

namespace VG_Catalog\Domain\UseCases\Cart\Handlers;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Application\Storage\CookieStorage;
use VG_Catalog\Domain\Interfaces\CartHandlerInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Product\Product;

class CookieCartHandler implements CartHandlerInterface
{

    public function __construct(
        protected CookieStorage $storage = new CookieStorage('vg_cart')
    ){}

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $productIds = [];
        $optionsIds = [];
        $carts = $this->storage->get();
        $result = [];

        if (empty($carts)) {
            if ($paginationDTO->limit === 0) {
                return [];
            }
            return paginateArray([], $paginationDTO->limit, $paginationDTO->page);
        }

        foreach ($carts as $cart) {
            $productIds[] = $cart['product_id'];

            if (is_array($cart['option_ids']) && !empty($cart['option_ids'])) {
                foreach ($cart['option_ids'] as $optionId) {
                    $optionsIds[] = $optionId;
                }
            }
        }

        $products = Product::query()->whereIn('post_id', array_unique($productIds))->get()->keyBy('post_id');
        $options = Option::query()->whereIn('post_id', array_unique($optionsIds))->get()->keyBy('post_id');

        foreach ($carts as $key => $cart) {
            $cartOptions = [];

            if (is_array($cart['option_ids']) && !empty($cart['option_ids'])) {
                $cartOptions = array_map(function ($optionId) use ($options) {
                    return $options[$optionId] ?? null;
                }, $cart['option_ids']);
            }

            $result[] = [
                'id' => $key,
                'quantity' => $cart['quantity'],
                'product_id' => $cart['product_id'],
                'product' => $products[$cart['product_id']] ?? null,
                'options' => $cartOptions,
            ];
        }
        if ($paginationDTO->limit === 0) {
            return $result;
        }
        return paginateArray($result, $paginationDTO->limit, $paginationDTO->page);
    }

    public function add(CartAddDTO $cartDTO): string
    {
        $cart = $this->storage->get();

        $cartId = $this->generateCartId($cart);

        $cart[$cartId] = [
            'product_id' => $cartDTO->product_id,
            'quantity' => $cartDTO->quantity,
            'option_ids' => $cartDTO->option_ids,
        ];

        $this->storage->set($cart);

        return $cartId;
    }

    private function generateCartId(array $cart): string
    {
        do {
            $id = bin2hex(random_bytes(16));
        } while (isset($cart[$id]));

        return $id;
    }

    public function update(CartUpdateDTO $cartDTO): string
    {
        $cart = $this->storage->get();
        $cartId = $cartDTO->cart_id;

        if (isset($cart[$cartId])) {
            $cart[$cartId]['quantity'] = $cartDTO->quantity;
            $this->storage->set($cart);
        }

        return $cartId;
    }

    public function remove(CartRemoveDTO $cartDTO): bool
    {
        $cart = $this->storage->get();
        $cartId = $cartDTO->cart_id;

        if (isset($cart[$cartId])) {
            unset($cart[$cartId]);
            $this->storage->set($cart);
            return true;
        }

        return false;
    }

    public function getTotal(array $cart_ids): array
    {
        $total['price'] = 0;
        $total['sale_price'] = 0;
        $carts = $this->storage->get();

        $products = Product::query()->whereIn('post_id', array_column($carts, 'product_id'))->get()->keyBy('post_id');
        foreach ($carts as $key => $cart) {
            if (in_array($key, $cart_ids)) {
                $product = $products[$cart['product_id']];
                $total['price'] += $product->price * $cart['quantity'];
                $total['sale_price'] += ($product->sale_price ?? $product->price) * $cart['quantity'];
            }
        }
        return $total;
    }
}
