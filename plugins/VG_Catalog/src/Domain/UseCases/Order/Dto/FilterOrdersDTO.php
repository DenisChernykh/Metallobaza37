<?php

namespace VG_Catalog\Domain\UseCases\Order\Dto;

use OpenApi\Attributes as OA;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;

#[OA\Schema(
    schema: 'FiltersOrdersDTO'
)]
class FilterOrdersDTO extends PaginationDTO
{
    #[OA\Property(
        property: 'status',
        type: 'string',
        enum: [OrderStatus::COMPLETED, OrderStatus::CANCELED, OrderStatus::WAITING, OrderStatus::PROGRESS],
        example: 'PROGRESS'
    )]
    public ?string $status;
}