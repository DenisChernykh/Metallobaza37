<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\ValidationMessage;

#[OA\Schema(
    schema: 'GroupOptionIdDTO'
)]
class GroupOptionIdDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'group_option_id',
        type: 'int',
        example: 1
    )]
    #[Assert\NotBlank(message: ValidationMessage::REQUIRED)]
    #[Assert\Type('int', message: ValidationMessage::TYPES['int'])]
    public string $group_option_id;
}