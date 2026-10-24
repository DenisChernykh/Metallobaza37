<?php

namespace VG_Catalog\Domain\UseCases\Cart;

use Illuminate\Database\Eloquent\Model;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Product\Product;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Cart',
    title: 'Cart',
    description: 'Cart model',
    required: ['id', 'user_id', 'product_id', 'quantity', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'int',
            description: 'Cart item id',
            example: 1
        ),
        new OA\Property(
            property: 'user_id',
            type: 'int',
            description: 'User id',
            example: 1
        ),
        new OA\Property(
            property: 'product_id',
            type: 'int',
            description: 'Product id',
            example: 1
        ),
        new OA\Property(
            property: 'quantity',
            type: 'int',
            description: 'Product quantity',
            example: 1
        ),
        new OA\Property(
            property: 'options',
            items: new OA\Items(ref: '#/components/schemas/CartOptions')
        ),
        new OA\Property(
            property: 'product',
            ref: '#/components/schemas/Product'
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when cart created',
            example: '2000-01-01 00:00:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when cart updated',
            example: '2000-01-01 00:00:00'
        )
    ]
)]
class Cart extends Model
{
    public $timestamps = true;
    protected $table = 'vg_catalog_cart';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'product_id', 'quantity', 'option_id'];

    public function product()
    {
        return $this->belongsTo(Product::class, ownerKey: 'post_id');
    }

    public function options()
    {
        return $this->hasMany(CartOptions::class, 'cart_id');
    }
}
