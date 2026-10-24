<?php

namespace VG_Catalog\Domain\UseCases\Option\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: "OptionIdDTO"
)]
class OptionIdDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'option_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type('int', message: ValidationMessage::TYPES['int'])]
    public int $option_id;
}