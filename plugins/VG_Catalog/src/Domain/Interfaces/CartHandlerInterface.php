<?php

namespace VG_Catalog\Domain\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;

interface CartHandlerInterface
{
    public function add(CartAddDTO $cartDTO);

    public function update(CartUpdateDTO $cartDTO);

    public function remove(CartRemoveDTO $cartDTO);

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator;

    public function getTotal(array $cart_ids): array;
}
