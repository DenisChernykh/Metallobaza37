<?php

namespace VG_Catalog\Domain\Support\Dto;

use OpenApi\Attributes as OA;
use Spatie\DataTransferObject\DataTransferObject;

#[OA\Schema(
    schema: 'PaginationDTO'
)]
class PaginationDTO extends DataTransferObject
{
    #[OA\Property(
        property: 'page',
        type: 'int',
        example: 1,
        default: 1
    )]
    public ?int $page = 1;

    #[OA\Property(
        property: 'limit',
        type: 'int',
        description: 'For limit 0 there is no pagination',
        example: 10,
        default: 10
    )]
    public ?int $limit = 10;

}