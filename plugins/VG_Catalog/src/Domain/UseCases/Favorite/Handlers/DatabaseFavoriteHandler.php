<?php

namespace VG_Catalog\Domain\UseCases\Favorite\Handlers;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Interfaces\FavoriteHandleInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Favorite\FavoriteRepository;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;

class DatabaseFavoriteHandler implements FavoriteHandleInterface
{
    protected FavoriteRepository $favoriteRepository;

    public function __construct()
    {
        $this->favoriteRepository = new FavoriteRepository();
    }

    public function toggleFavorite(ProductIdDTO $productIdDTO): string
    {
        return $this->favoriteRepository->toggleFavorite($productIdDTO);
    }

    public function isFavorite(ProductIdDTO $productIdDTO): bool
    {
        return $this->favoriteRepository->isFavorite($productIdDTO);
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        return $this->favoriteRepository->getList($paginationDTO);
    }
}