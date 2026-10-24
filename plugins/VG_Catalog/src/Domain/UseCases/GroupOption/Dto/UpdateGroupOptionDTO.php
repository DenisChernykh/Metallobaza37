<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateGroupOptionDTO'
)]
class UpdateGroupOptionDTO extends GroupOptionIdDTO
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        example: 'some title'
    )]
    public ?string $title;

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