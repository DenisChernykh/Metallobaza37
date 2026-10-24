<?php

namespace VG_Catalog\Domain\Factory;

use VG_Catalog\Domain\Interfaces\FavoriteHandleInterface;
use VG_Catalog\Domain\UseCases\Favorite\Handlers\CookieFavoriteHandler;
use VG_Catalog\Domain\UseCases\Favorite\Handlers\DatabaseFavoriteHandler;

class FavoriteHandlerFactory
{
    public static function make(mixed $userId): FavoriteHandleInterface
    {
        return $userId ? new DatabaseFavoriteHandler() : new CookieFavoriteHandler();
    }
}