<?php

namespace VG_Catalog\Domain\UseCases\Cart\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: 'CartUpdateDTO',
    required: [
        'cart_id'
    ]
)]
class CartUpdateDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'cart_id',
        type: 'string',
        example: 'h624a6mb42m4c4'
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type(type: 'string', message: ValidationMessage::TYPES['string'])]
    public string $cart_id;

    #[OA\Property(
        property: 'quantity',
        type: 'int',
        example: 1
    )]
    #[Assert\Type(type: 'int', message: ValidationMessage::TYPES['int'])]
    #[Assert\GreaterThan(value: 0, message: ValidationMessage::LENGTH['greater'].'0')]
    public int $quantity = 1;
}
