<?php

namespace VG_Catalog\Domain\UseCases\Categories\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateCategoryDTO",
    required: [
        "name"
    ]
)]
class UpdateCategoryDTO extends CategoryIdDTO
{
    #[OA\Property(
        property: "name",
        type: "string",
        example: "category name"
    )]
    public ?string $name;

    #[OA\Property(
        property: "slug",
        type: "string",
        example: "category slug"
    )]
    public ?string $slug;

    #[OA\Property(
        property: "description",
        type: "string",
        example: "category description"
    )]
    public ?string $description;

    #[OA\Property(
        property: "parent_id",
        type: "int",
        description: "ID of the parent term",
        example: 2
    )]
    public ?int $parent_id;

    #[OA\Property(
        property: "image",
        type: "int",
        description: "Image ID",
        example: 12
    )]
    public ?int $image;
}