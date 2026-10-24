<?php

use Illuminate\Pagination\LengthAwarePaginator;

function applyQueryFilters($query, array $filters): void
{
    foreach ($filters as $filter) {
        $field = $filter['field'] ?? null;
        $operation = $filter['operation'] ?? 'eq';
        $value = $filter['value'] ?? null;

        if (!$field || $value === null || $value === '') {
            continue;
        }

        match($operation) {
            'eq' => $query->where($field, $value),
            'neq' => $query->where($field, '!=', $value),
            'gt' => $query->where($field, '>', $value),
            'gte' => $query->where($field, '>=', $value),
            'lt' => $query->where($field, '<', $value),
            'lte' => $query->where($field, '<=', $value),
            'like' => $query->where($field, 'like', '%' . $value . '%'),
            'starts_with' => $query->where($field, 'like', $value . '%'),
            'ends_with' => $query->where($field, 'like', '%' . $value),
            'in' => $query->whereIn($field, is_array($value) ? $value : [$value]),
            'not_in' => $query->whereNotIn($field, is_array($value) ? $value : [$value]),
            'between' => is_array($value) && count($value) === 2
                ? $query->whereBetween($field, $value)
                : null,
            'null' => $query->whereNull($field),
            'not_null' => $query->whereNotNull($field),
            default => null
        };
    }
}

function paginateArray(array $array, $perPage = 10, $page = 1)
{
    $offset = ($page - 1) * $perPage;

    return new LengthAwarePaginator(
        array_slice($array, $offset, $perPage),
        count($array),
        $perPage,
        $page,
        [
            'path' => '',
            'pageName' => 'page',
        ]
    );
}