<?php

namespace VG_Catalog\Domain\UseCases\Order;

use Illuminate\Database\Eloquent\Model;
use VG_Catalog\Domain\UseCases\Product\Product;
use OpenApi\Attributes as OA;
#[OA\Schema(
    schema: 'OrderItems',
    title: 'Order items',
    description: 'OrderItem model',
    required: ['id', 'order_id', 'product', 'quantity', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            description: 'Order item id',
            example: 1
        ),
        new OA\Property(
            property: 'product',
            ref: '#/components/schemas/Product',
        ),
        new OA\Property(
            property: 'quantity',
            type: 'int',
            example: 10
        ),
        new OA\Property(
            property: 'options',
            items: new OA\Items(ref: '#/components/schemas/Option'),
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when product created',
            example: '2000-01-01 00:00:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when product updated',
            example: '2000-01-01 00:00:00'
        )
    ]
)]
class OrderItem extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $table = 'vg_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];

    public function options()
    {
        return $this->hasMany(OrderItemOption::class, 'order_item_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, ownerKey: 'post_id');
    }
}