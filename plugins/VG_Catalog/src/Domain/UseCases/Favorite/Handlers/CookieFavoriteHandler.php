<?php

namespace VG_Catalog\Domain\UseCases\Favorite\Handlers;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Application\Storage\CookieStorage;
use VG_Catalog\Domain\Interfaces\FavoriteHandleInterface;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Product;

class CookieFavoriteHandler implements FavoriteHandleInterface
{

    public function __construct(
        protected CookieStorage $storage = new CookieStorage('vg_favorites')
    ){}

    public function toggleFavorite(ProductIdDTO $productIdDTO)
    {
        $favorites = $this->storage->get();

        if ($favorites[$productIdDTO->product_id]) {
            $action = 'removeFromFavorites';
            unset($favorites[$productIdDTO->product_id]);
        }
        else {
            $action = 'addToFavorites';
            $favorites[$productIdDTO->product_id] = $productIdDTO->product_id;
        }

        $this->storage->set($favorites);

        return $action;
    }

    public function isFavorite(ProductIdDTO $productIdDTO): bool
    {
        $favorites = $this->storage->get();
        return isset($favorites[$productIdDTO->product_id]);
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $products = Product::whereIn('post_id', $this->storage->get());
        if ($paginationDTO->limit === 0) {
            return $products->get()->toArray();
        }
        return $products->paginate($paginationDTO->limit, page: $paginationDTO->page);
    }
}