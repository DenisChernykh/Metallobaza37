<?php

namespace VG_Catalog\Domain\UseCases\Product\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: 'ProductIdDTO',
    type: 'object',
    required: [
        'product_id',
    ]
)]
class ProductIdDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'product_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type('int', message: ValidationMessage::TYPES['int'])]
    public ?int $product_id;

}