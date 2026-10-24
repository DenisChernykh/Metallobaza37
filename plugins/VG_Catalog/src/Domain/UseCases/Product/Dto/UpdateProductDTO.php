<?php

namespace VG_Catalog\Domain\UseCases\Product\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProductDTO',
)]
class UpdateProductDTO extends ProductIdDTO
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'Product title'
    )]
    public ?string $title;

    #[OA\Property(
        property: 'description',
        type: 'string',
        example: 'Product description'
    )]
    public ?string $description;

    #[OA\Property(
        property: 'price',
        type: 'float',
        example: 10.0
    )]
    public ?float $price;

    #[OA\Property(
        property: 'sale_price',
        type: 'float',
        example: 9.0
    )]
    public ?float $sale_price;

    #[OA\Property(
        property: 'unit_id',
        type: 'int',
        example: 1
    )]
    public ?string $unit_id;

    #[OA\Property(
        property: 'sku',
        type: 'string',
        example: 'article'
    )]
    public ?string $sku;

    #[OA\Property(
        property: 'status',
        type: 'string',
        example: 'publish'
    )]
    public ?string $status;

    #[OA\Property(
        property: 'main_image',
        type: 'int',
        example: 1
    )]
    public ?int $main_image;
    #[OA\Property(
        property: 'gallery',
        type: 'object',
        example: [1,2]
    )]
    public ?array $gallery;

    #[OA\Property(
        property: 'categories',
        type: 'object',
        example: [1,2]
    )]
    public ?array $categories;
}