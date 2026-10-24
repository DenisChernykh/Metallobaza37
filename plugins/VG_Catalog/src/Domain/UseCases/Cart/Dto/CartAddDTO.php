<?php

namespace VG_Catalog\Domain\UseCases\Cart\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: 'CartAddDTO',
    required: [
        'product_id'
    ]
)]
class CartAddDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'product_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type(type: 'int', message: ValidationMessage::TYPES['int'])]
    #[Assert\GreaterThan(value: 0, message: ValidationMessage::LENGTH['greater'].'0')]
    public int $product_id;

    #[OA\Property(
        property: 'quantity',
        type: 'int',
        example: 1
    )]
    #[Assert\Type(type: 'int', message: ValidationMessage::TYPES['int'])]
    #[Assert\GreaterThan(value: 0, message: ValidationMessage::LENGTH['greater'].'0')]
    public int $quantity = 1;

    #[OA\Property(
        property: 'option_ids',
        type: 'array',
        example: [1, 2]
    )]
    public ?array $option_ids = null;
}