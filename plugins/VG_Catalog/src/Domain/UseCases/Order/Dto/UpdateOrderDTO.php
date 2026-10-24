<?php

namespace VG_Catalog\Domain\UseCases\Order\Dto;

use OpenApi\Attributes as OA;
use VG_Catalog\Domain\UseCases\Order\Enums\OrderStatus;

#[OA\Schema(
    schema: 'UpdateOrderDTO'
)]
class UpdateOrderDTO extends OrderIdDTO
{
    #[OA\Property(
        property: 'status',
        type: 'string',
        enum: [OrderStatus::COMPLETED, OrderStatus::CANCELED, OrderStatus::WAITING, OrderStatus::PROGRESS],
        example: 'COMPLETED',
    )]
    public ?string $status;
}