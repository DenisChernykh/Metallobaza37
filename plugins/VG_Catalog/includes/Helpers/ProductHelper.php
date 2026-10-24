<?php

namespace VG_Catalog\Helpers;

use VG_Catalog\Domain\UseCases\Product\Product;

class ProductHelper
{
    protected Product $product;
    protected $post;

    public function __construct(int|Product $product)
    {
        $this->product = $product instanceof Product ? $product : Product::find($product);
        $this->post = get_post($this->product->post_id);
    }

    public function getHtmlPrice()
    {
        return $this->product->price.' ₽';
    }

    public function getHtmlPriceSale()
    {
        return $this->product->sale_price.' ₽';
    }

    public function hasSpecialPrice()
    {
        return $this->product->special_price !== null;
    }
}