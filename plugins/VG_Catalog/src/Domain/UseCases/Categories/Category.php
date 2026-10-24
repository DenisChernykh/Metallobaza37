<?php

namespace VG_Catalog\Domain\UseCases\Categories;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    title: 'Category',
    required: ['id', 'name', 'slug', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'int',
            description: 'Term id',
            example: 1
        ),
        new OA\Property(
            property: 'name',
            type: 'string',
            description: 'Category name',
            example: 'Category name'
        ),
        new OA\Property(
            property: 'slug',
            type: 'string',
            description: 'Category slug',
            example: 'Category-slug'
        ),
        new OA\Property(
            property: 'description',
            type: 'string',
            description: 'Category description',
            example: 'Category description'
        ),
        new OA\Property(
            property: 'parent',
            type: 'int',
            description: 'Category parent id',
            example: 2
        ),
        new OA\Property(
            property: 'link',
            type: 'string',
            description: 'Category link',
            example: 'http://example.com/?vg_product_category=api_link'
        ),
        new OA\Property(
            property: 'image',
            type: 'string',
            description: 'Category image url',
            example: 'http://example.com/wp-content/uploads/2025/10/test.jpg'
        )
    ]
)]
class Category extends Model
{

}