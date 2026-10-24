<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Factory\FavoriteHandlerFactory;
use VG_Catalog\Domain\Interfaces\FavoriteHandleInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;

class FavoriteService
{
    protected FavoriteHandleInterface $handler;
    public function __construct()
    {
        $userId = get_current_user_id();
        $this->handler = FavoriteHandlerFactory::make($userId ?? null);

    }

    public function toggleFavorite(ProductIdDTO $productIdDTO): string
    {
        return $this->handler->toggleFavorite($productIdDTO);
    }

    public function isFavorite(ProductIdDTO $productIdDTO): bool
    {
        return $this->handler->isFavorite($productIdDTO);
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        return $this->handler->getList($paginationDTO);
    }
}