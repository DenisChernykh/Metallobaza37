<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Option\Actions\CreateOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\DeleteOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\GetListOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\GetOptionByIdAction;
use VG_Catalog\Domain\UseCases\Option\Actions\UpdateOptionAction;
use VG_Catalog\Domain\UseCases\Option\Dto\CreateOptionDTO;
use VG_Catalog\Domain\UseCases\Option\Dto\OptionIdDTO;
use VG_Catalog\Domain\UseCases\Option\Dto\UpdateOptionDTO;
use VG_Catalog\Domain\UseCases\Option\Option;

class OptionService
{

    public function create(CreateOptionDTO $optionDTO, CreateOptionAction $action): Option
    {
        return $action($optionDTO);
    }

    public function getById(OptionIdDTO $optionDTO, GetOptionByIdAction $action): Option|\InvalidArgumentException
    {
        return $action($optionDTO);
    }

    public function getList(PaginationDTO $paginationDTO, GetListOptionAction $action): array|LengthAwarePaginator
    {
        return $action($paginationDTO);
    }

    public function update(UpdateOptionDTO $optionDTO, UpdateOptionAction $action): bool|\InvalidArgumentException
    {
        return $action($optionDTO);
    }

    public function delete(OptionIdDTO $optionDTO, DeleteOptionAction $action): bool|\InvalidArgumentException
    {
        return $action($optionDTO);
    }
}