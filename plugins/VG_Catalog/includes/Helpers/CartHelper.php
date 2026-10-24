<?php

namespace VG_Catalog\Helpers;

use VG_Catalog\Domain\Factory\CartHandlerFactory;

class CartHelper
{
    public function getTotalPrice(array $cart_ids): array
    {
        $cart = CartHandlerFactory::make(get_current_user_id());

        return $cart->getTotal($cart_ids);
    }
}