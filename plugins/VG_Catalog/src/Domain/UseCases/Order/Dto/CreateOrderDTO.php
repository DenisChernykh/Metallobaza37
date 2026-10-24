<?php

namespace VG_Catalog\Domain\UseCases\Order\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateOrderDTO',
    required: [
        'title',
        'email',
        'phone',
        'cart_ids'
    ]
)]
class CreateOrderDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'name',
        type: 'string',
        example: 'User name'
    )]
    public ?string $name;

    #[OA\Property(
        property: 'additional',
        type: 'array',
        example: ['email' => 'example@gmail.com']
    )]
    public ?array $additional;

    #[OA\Property(
        property: 'phone',
        type: 'string',
        example: 'User phone number'
    )]
    public ?string $phone;

    #[OA\Property(
        property: 'cart_ids',
        type: 'array',
        example: [1, 2]
    )]
    public ?array $cart_ids;

    public static function fromArray(array $data)
    {
        global $orderConfig;
        $requiredFields = $orderConfig['required'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Field '$field' is required", 422);
            }
        }
        $requiredAdditional = $orderConfig['additional']['required'];
        foreach ($requiredAdditional as $field) {
            if (!isset($data['additional'][$field])) {
                throw new \InvalidArgumentException("Additional field '$field' is required", 422);
            }
        }

        return new self($data);
    }
}