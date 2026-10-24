<?php

namespace VG_Catalog\Domain\UseCases\Order;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;

#[OA\Schema(
    schema: 'Order',
    title: 'Order',
    description: 'Order model',
    required: ['id', 'name', 'phone', 'email', 'items', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            description: 'Order id',
            example: 1
        ),
        new OA\Property(
            property: 'name',
            type: 'string',
            description: 'User name',
            example: 'FirstName Lastname'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            description: 'User phone',
            example: '79009009090'
        ),
        new OA\Property(
            property: 'additional',
            type: 'array',
            description: 'Additional data',
            example: ['email' => 'example@gmail.com']
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            description: 'Order items',
            items: new OA\Items(ref: '#/components/schemas/OrderItems'),
        ),
        new OA\Property(
            property: 'user_id',
            type: 'int',
            example: 1
        ),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: [OrderStatus::COMPLETED, OrderStatus::CANCELED, OrderStatus::WAITING, OrderStatus::PROGRESS],
            example: 'PROGRESS'
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
class Order extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $table = 'vg_orders';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'additional',
        'status'
    ];

    public function getAdditionalAttribute()
    {
        return json_decode($this->attributes['additional'], true) ?? [];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}