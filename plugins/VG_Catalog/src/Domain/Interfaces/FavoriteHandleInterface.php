<?php

namespace VG_Catalog\Domain\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;

interface FavoriteHandleInterface
{
    public function toggleFavorite(ProductIdDTO $productIdDTO);
    public function isFavorite(ProductIdDTO $productIdDTO);
    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator;
}
