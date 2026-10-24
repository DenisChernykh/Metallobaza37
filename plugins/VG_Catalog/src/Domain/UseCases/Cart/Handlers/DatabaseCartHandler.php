<?php

namespace VG_Catalog\Domain\UseCases\Cart\Handlers;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Interfaces\CartHandlerInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Cart;
use VG_Catalog\Domain\UseCases\Cart\CartRepository;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;

class DatabaseCartHandler implements CartHandlerInterface
{
    protected CartRepository $cartRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        return $this->cartRepository->getList($paginationDTO);
    }

    public function add(CartAddDTO $cartDTO): string
    {
        return $this->cartRepository->addToCart($cartDTO);
    }

    public function update(CartUpdateDTO $cartDTO): string
    {
        return $this->cartRepository->updateToCart($cartDTO);
    }

    public function remove(CartRemoveDTO $cartDTO): bool
    {
        return $this->cartRepository->removeFromCart($cartDTO);
    }

    public function getTotal(array $cart_ids): array
    {
        $total['full_price'] = 0;
        $total['sale_price'] = 0;
        $carts = Cart::with('product')->find($cart_ids);
        foreach ($carts as $cart) {
            $quantity = $cart->quantity;
            $product = $cart->product;

            $total['full_price'] += $product->price * $quantity;

            $salePrice = $product->sale_price;
            if (isset($salePrice) && $salePrice > 0) {
                $total['sale_price'] += $salePrice * $quantity;
            }
            else {
                $total['sale_price'] += $product->price * $quantity;
            }
        }
        return $total;
    }
}
