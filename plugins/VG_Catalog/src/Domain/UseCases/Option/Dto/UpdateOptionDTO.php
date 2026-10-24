<?php

namespace VG_Catalog\Domain\UseCases\Option\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateOptionDTO"
)]
class UpdateOptionDTO extends OptionIdDTO
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'some title'
    )]
    public ?string $title;

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
}