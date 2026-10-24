<?php

namespace VG_Catalog\Domain\UseCases\Categories\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: "CategoryIdDTO",
    required: [
        "category_id"
    ]
)]
class CategoryIdDTO extends DataTransferObject
{
    #[OA\Property(
        property: "category_id",
        type: "int",
        description: "Term ID",
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    public ?int $category_id;
}