<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Factory\CartHandlerFactory;
use VG_Catalog\Domain\Interfaces\CartHandlerInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;

class CartService
{
    protected CartHandlerInterface $handler;

    public function __construct()
    {
        $userId = get_current_user_id();
        $this->handler = CartHandlerFactory::make($userId ?? null);
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        return $this->handler->getList($paginationDTO);
    }

    public function addToCart(CartAddDTO $cartAddDto): string
    {
        return $this->handler->add($cartAddDto);
    }

    public function updateToCart(CartUpdateDTO $cartUpdateDto): string
    {
        return $this->handler->update($cartUpdateDto);
    }

    public function removeFromCart(CartRemoveDTO $cartRemoveDto): bool
    {
        return $this->handler->remove($cartRemoveDto);
    }

    public function getCartTotal(): array
    {
        $cartItems = $this->getList(new PaginationDTO(['limit' => 0]));
        $cartIds = array_column($cartItems, 'id');

        return $this->handler->getTotal($cartIds);
    }
}
