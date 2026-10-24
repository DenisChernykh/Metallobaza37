<?php

namespace VG_Catalog\Domain\UseCases\Product\Dto;

use OpenApi\Attributes as OA;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;

#[OA\Schema(
    schema: 'FiltersProductDTO',
)]
class FiltersProductDTO extends PaginationDTO
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'Product title'
    )]
    public ?string $title;
    #[OA\Property(
        property: 'price_min',
        type: 'float',
        example: 8.0
    )]
    public ?float $price_min;
    #[OA\Property(
        property: 'price_max',
        type: 'float',
        example: 10.0
    )]
    public ?float $price_max;

    #[OA\Property(
        property: 'category_id',
        type: 'int',
        example: 1
    )]
    public ?int $category_id;
    #[OA\Property(
        property: 'sort_by',
        type: 'string',
        example: 'created_at'
    )]
    public ?string $sort_by = 'created_at';
    #[OA\Property(
        property: 'sort_type',
        type: 'string',
        example: 'asc'
    )]
    public ?string $sort_type = 'asc';
}