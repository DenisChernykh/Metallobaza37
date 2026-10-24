<?php

namespace VG_Catalog\Domain\UseCases\Order\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderIdDTO',
    required: [
        'order_id',
    ]
)]
class OrderIdDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'product_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type('int', message: ValidationMessage::TYPES['int'])]
    public ?int $order_id;
}