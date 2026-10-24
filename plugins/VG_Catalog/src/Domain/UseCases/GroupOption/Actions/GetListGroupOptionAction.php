<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;

class GetListGroupOptionAction
{
    public function __invoke(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $groupOptions = GroupOption::query()->with('options');
        if ($paginationDTO->limit === 0) {
            return $groupOptions->get()->toArray();
        }
        return $groupOptions->paginate($paginationDTO->limit, page: $paginationDTO->page);
    }
}