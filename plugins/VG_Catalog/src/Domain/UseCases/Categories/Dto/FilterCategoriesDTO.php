<?php

namespace VG_Catalog\Domain\UseCases\Categories\Dto;

use OpenApi\Attributes as OA;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;

#[OA\Schema(
    schema: 'FilterCategoriesDTO',
)]
class FilterCategoriesDTO extends PaginationDTO
{
    #[OA\Property(
        property: 'parent_id',
        type: 'int',
        description: 'Parent id',
        example: 1
    )]
    public ?int $parent_id;
}