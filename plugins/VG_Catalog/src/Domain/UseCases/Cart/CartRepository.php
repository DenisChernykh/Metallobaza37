<?php

namespace VG_Catalog\Domain\UseCases\Cart;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;
use VG_Catalog\Domain\UseCases\Product\Product;

class CartRepository
{
    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $userId = get_current_user_id();
        $cart = Cart::with('product', 'options.option')
            ->where('user_id', $userId);
        if ($paginationDTO->limit === 0) {
            return $cart->get()->toArray();
        }
        return $cart->paginate($paginationDTO->limit, page: $paginationDTO->page);
    }

    public function addToCart(CartAddDTO $cartAddDTO): string
    {
        $userId = get_current_user_id();
        $product = Product::where('post_id', $cartAddDTO->product_id)->first();

        if ($product === null) {
            throw new \InvalidArgumentException('Product not found');
        }

        $cart = Cart::create([
            'user_id' => $userId,
            'product_id' => $product->post_id,
            'quantity' => $cartAddDTO->quantity
        ]);

        if ($cartAddDTO->option_ids) {
            foreach ($cartAddDTO->option_ids as $optionId) {
                $cart->options()->create(['option_id' => $optionId]);
            }
        }

        return $cart->id;
    }

    public function updateToCart(CartUpdateDTO $cartUpdateDTO): string
    {
        $userId = get_current_user_id();
        $cart = Cart::updateOrCreate(
            [
                'user_id' => $userId,
                'id' => $cartUpdateDTO->cart_id,
            ],
            [
                'quantity' => $cartUpdateDTO->quantity
            ]
        );

        if ($cartUpdateDTO->option_ids) {
            $cart->options()->delete();
            foreach ($cartUpdateDTO->option_ids as $optionId) {
                $cart->options()->create(['option_id' => $optionId]);
            }
        }

        return $cart->id;
    }

    public function removeFromCart(CartRemoveDTO $cartRemoveDTO): bool
    {
        $userId = get_current_user_id();
        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $cartRemoveDTO->cart_id)
            ->first();

        if ($cartItem) {
            return $cartItem->delete();
        }

        return false;
    }
}
