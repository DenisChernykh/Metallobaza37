<?php

namespace VG_Catalog\Domain\UseCases\GroupOption;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Product\ProductGroup;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GroupOption',
    title: 'Group option',
    description: 'Group option model',
    required: ['id', 'post_id', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'int',
            description: 'Option id',
            example: 1
        ),
        new OA\Property(
            property: 'post_id',
            type: 'int',
            description: 'Post option id',
            example: 1
        ),
        new OA\Property(
            property: 'title',
            type: 'string',
            description: 'Option title',
            example: 1
        ),
        new OA\Property(
            property: 'required',
            type: 'bool',
            description: 'Required group to choose',
            example: true
        ),
        new OA\Property(
            property: 'multiple',
            type: 'bool',
            description: 'Group multiple choose',
            example: true
        ),
        new OA\Property(
            property: 'options',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Option')
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'Date time when option created',
            example: '2000-01-01 00:00:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'Date time when option updated',
            example: '2000-01-01 00:00:00'
        )
    ]
)]
class GroupOption extends Model
{
    protected $table = 'vg_group_options';
    protected $fillable = [
        'post_id',
        'required',
        'multiple'
    ];

    protected $appends = [
        'title',
        'required',
        'multiple'
    ];

    public function getTitleAttribute()
    {
        return get_the_title($this->post_id);
    }

    public function getRequiredAttribute()
    {
        return $this->attributes['required'] === 'yes' ? true : false;
    }

    public function getMultipleAttribute()
    {
        return $this->attributes['multiple'] === 'yes' ? true : false;
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class, 'group_id', 'post_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'group_id', 'post_id');
    }
}