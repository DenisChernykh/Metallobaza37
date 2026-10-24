<?php

namespace VG_Catalog\Domain\UseCases\Option;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Option',
    title: 'Option',
    description: 'Option model',
    required: ['id', 'post_id', 'group_id', 'created_at', 'updated_at'],
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
            property: 'group_id',
            type: 'int',
            description: 'Option group id',
            example: 1
        ),
        new OA\Property(
            property: 'additional',
            type: 'array',
            description: 'Additional params',
            example: [
                [
                    'title' => 'some title',
                    'value' => 'some value'
                ],
                [
                    'title' => 'some title 2',
                    'value' => 'some value 2'
                ]
            ]
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
class Option extends Model
{
    protected $table = 'vg_options';
    protected $fillable = [
        'post_id',
        'group_id',
        'additional'
    ];

    protected $appends = [
        'title',
        'additional'
    ];

    public function getTitleAttribute()
    {
        return get_the_title($this->post_id);
    }

    public function getAdditionalAttribute()
    {
        return json_decode($this->attributes['additional'], true);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupOption::class, 'group_id', 'post_id');
    }
}