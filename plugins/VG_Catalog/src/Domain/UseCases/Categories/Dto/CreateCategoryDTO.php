<?php

namespace VG_Catalog\Domain\UseCases\Categories\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: "CreateCategoryDTO",
    required: [
        "name"
    ]
)]
class CreateCategoryDTO extends DataTransferObject
{
    #[OA\Property(
        property: "name",
        type: "string",
        example: "category name"
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public string $name;

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

    #[OA\Property(
        property: "weight",
        type: "int",
        description: "Category weight",
        example: 10
    )]
    public ?int $weight;
}