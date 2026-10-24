<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: 'CreateGroupOptionDTO',
    required: [
        'title'
    ]
)]
class CreateGroupOptionDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'some title'
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public string $title;

    #[OA\Property(
        property: 'required',
        type: 'bool',
        example: true
    )]
    public ?bool $required;

    #[OA\Property(
        property: 'multiple',
        type: 'bool',
        example: true
    )]
    public ?bool $multiple;
}