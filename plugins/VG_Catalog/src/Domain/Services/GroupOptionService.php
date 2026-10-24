<?php

namespace VG_Catalog\Domain\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\CreateGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\DeleteGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\GetGroupOptionByIdAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\GetListGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\UpdateGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\CreateGroupOptionDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\GroupOptionIdDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\UpdateGroupOptionDTO;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;

class GroupOptionService
{

    public function create(CreateGroupOptionDTO $groupOptionDTO, CreateGroupOptionAction $action): GroupOption
    {
        return $action($groupOptionDTO);
    }

    public function getById(GroupOptionIdDTO $groupOptionDTO, GetGroupOptionByIdAction $action): GroupOption|\InvalidArgumentException
    {
        return $action($groupOptionDTO);
    }

    public function getList(PaginationDTO $paginationDTO, GetListGroupOptionAction $action): array|LengthAwarePaginator
    {
        return $action($paginationDTO);
    }

    public function update(UpdateGroupOptionDTO $groupOptionDTO, UpdateGroupOptionAction $action): bool|\InvalidArgumentException
    {
        return $action($groupOptionDTO);
    }

    public function delete(GroupOptionIdDTO $groupOptionDTO, DeleteGroupOptionAction $action): bool|\InvalidArgumentException
    {
        return $action($groupOptionDTO);
    }
}