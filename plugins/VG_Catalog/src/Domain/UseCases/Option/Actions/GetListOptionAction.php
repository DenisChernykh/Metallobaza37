<?php

namespace VG_Catalog\Domain\UseCases\Option\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Option\Option;

class GetListOptionAction
{
    public function __invoke(PaginationDTO $paginationDTO): array|LengthAwarePaginator
    {
        $options = Option::query()->with('group');
        if ($paginationDTO->limit === 0) {
            return $options->get()->toArray();
        }
        return $options->paginate($paginationDTO->limit, page: $paginationDTO->page);
    }
}