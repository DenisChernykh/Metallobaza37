<?php

namespace VG_Catalog\Domain\UseCases\Product\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: "CreateProductDTO",
    required: [
        "title",
        "description",
        "price",
        "sale_price",
        "sku"
    ]
)]
class CreateProductDTO extends DataTransferObject
{
    #[OA\Property(
        property: "post_id",
        type: "int",
        description: "Post ID, if post exsist",
        example: 1
    )]
    public ?int $post_id;

    #[OA\Property(
        property: "title",
        type: "string",
        example: "Product title"
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public ?string $title;

    #[OA\Property(
        property: "description",
        type: "string",
        example: "Product description"
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public ?string $description;

    #[OA\Property(
        property: "price",
        type: "float",
        example: 10.0
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public ?float $price;

    #[OA\Property(
        property: "sale_price",
        type: "float",
        example: 9.0
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public ?float $sale_price;

    #[OA\Property(
        property: "main_image",
        type: "int",
        example: 1
    )]
    public ?int $main_image;

    #[OA\Property(
        property: "gallery",
        type: "object",
        example: [1,2]
    )]
    public ?array $gallery;

    #[OA\Property(
        property: "categories",
        type: "object",
        example: [1,2]
    )]
    public ?array $categories;

    #[OA\Property(
        property: "sku",
        type: "string",
        example: "article"
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public string $sku;

    #[OA\Property(
        property: "unit_id",
        type: "int",
        example: 1
    )]
    public ?string $unit_id;

    #[OA\Property(
        property: "status",
        type: "string",
        example: "publish"
    )]
    public ?string $status;

    #[OA\Property(
        property: "groups_options",
        type: "object",
        example: [1,2]
    )]
    public ?array $groups_options;
}