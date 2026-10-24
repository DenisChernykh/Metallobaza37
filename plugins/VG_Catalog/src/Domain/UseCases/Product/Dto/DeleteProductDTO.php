<?php

namespace VG_Catalog\Domain\UseCases\Product\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteProductDTO',
)]
class DeleteProductDTO extends ProductIdDTO
{

}