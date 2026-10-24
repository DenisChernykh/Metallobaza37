<?php

namespace VG_Catalog\Domain\Factory;

use VG_Catalog\Domain\Interfaces\CartHandlerInterface;
use VG_Catalog\Domain\UseCases\Cart\Handlers\CookieCartHandler;
use VG_Catalog\Domain\UseCases\Cart\Handlers\DatabaseCartHandler;

class CartHandlerFactory
{
    public static function make(mixed $userId): CartHandlerInterface
    {
        return $userId ? new DatabaseCartHandler() : new CookieCartHandler();
    }
}
