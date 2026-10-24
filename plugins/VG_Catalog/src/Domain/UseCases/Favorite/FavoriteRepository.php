<?php

namespace VG_Catalog\Domain\UseCases\Favorite;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Product;

class FavoriteRepository
{
    public function toggleFavorite(ProductIdDTO $productIdDTO): string
    {
        $userId = get_current_user_id();

        $favorites = get_user_meta($userId, 'vg_favorites', true) ?: [];
        if (in_array($productIdDTO->product_id, $favorites)) {
            $favorites = array_diff($favorites, [$productIdDTO->product_id]);
            update_user_meta($userId, 'vg_favorites', $favorites);

            return 'removeFromFavorites';
        } else {
            $favorites[] = $productIdDTO->product_id;
            update_user_meta($userId, 'vg_favorites', $favorites);

            return 'addToFavorites';
        }
    }

    public function isFavorite(ProductIdDTO $productIdDTO): bool
    {
        $userId = get_current_user_id();

        $favorites = get_user_meta($userId, 'vg_favorites', true);
        return is_array($favorites) && in_array($productIdDTO->product_id, $favorites);
    }

    public function getList(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $userId = get_current_user_id();

        $favorites = get_user_meta($userId, 'vg_favorites', true);

        $products = Product::whereIn('post_id', is_array($favorites) ? $favorites : []);
        if ($paginationDTO->limit === 0) {
            return $products->get()->toArray();
        }
        return $products->paginate($paginationDTO->limit, page: $paginationDTO->page);
    }
}