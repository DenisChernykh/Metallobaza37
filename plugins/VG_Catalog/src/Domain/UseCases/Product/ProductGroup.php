<?php

namespace VG_Catalog\Domain\UseCases\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductGroup',
    title: 'Product groups',
    description: 'Product groups model',
    required: ['id', 'product_id', 'group_id', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'int',
            description: 'Product group id',
            example: 1
        ),
        new OA\Property(
            property: 'product_id',
            type: 'int',
            description: 'Product id',
            example: 1
        ),
        new OA\Property(
            property: 'group_id',
            type: 'int',
            description: 'Group id',
            example: 1
        ),
        new OA\Property(
            property: 'group',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/GroupOption')
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'Date time when product group created',
            example: '2000-01-01 00:00:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'Date time when product group updated',
            example: '2000-01-01 00:00:00'
        )
    ]
)]
class ProductGroup extends Model
{
    protected $table = 'vg_product_groups';
    protected $fillable = [
        'product_id',
        'group_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupOption::class, 'group_id', 'post_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'post_id');
    }
}