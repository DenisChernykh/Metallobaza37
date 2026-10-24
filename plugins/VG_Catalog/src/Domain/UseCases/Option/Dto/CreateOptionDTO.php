<?php

namespace VG_Catalog\Domain\UseCases\Option\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: "CreateOptionDTO",
    required: [
        'title',
        'group_id',
    ]
)]
class CreateOptionDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'some title'
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public string $title;

    #[OA\Property(
        property: 'additional',
        type: 'array',
        example: [
            [
                'title' => 'some title',
                'value' => 'some value'
            ],
            [
                'title' => 'some title 2',
                'value' => 'some value 2'
            ]
        ]
    )]
    public ?array $additional;

    #[OA\Property(
        property: 'group_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type('int', message: ValidationMessage::TYPES['int'])]
    public int $group_id;
}