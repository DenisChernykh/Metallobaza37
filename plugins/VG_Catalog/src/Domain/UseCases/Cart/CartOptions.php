<?php

namespace VG_Catalog\Domain\UseCases\Cart;

use Illuminate\Database\Eloquent\Model;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Product\Product;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartOptions',
    title: 'Cart',
    description: 'Cart model',
    required: ['id', 'cart_id', 'option', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'int',
            description: 'Cart item id',
            example: 1
        ),
        new OA\Property(
            property: 'cart_id',
            type: 'int',
            description: 'Cart id',
            example: 1
        ),
        new OA\Property(
            property: 'option',
            ref: '#/components/schemas/Option',
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
class CartOptions extends Model
{
    public $timestamps = true;
    protected $table = 'vg_catalog_cart_options';
    protected $primaryKey = 'id';
    protected $fillable = ['cart_id', 'option_id'];

    public function option()
    {
        return $this->belongsTo(Option::class, ownerKey: 'post_id');
    }
}
