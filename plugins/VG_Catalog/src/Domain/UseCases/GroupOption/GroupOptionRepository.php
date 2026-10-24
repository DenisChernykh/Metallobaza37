<?php

namespace VG_Catalog\Domain\UseCases\GroupOption;

class GroupOptionRepository
{
    public function create(array $product): GroupOption
    {
        return GroupOption::create($product);
    }

    public function getById($id): GroupOption|null
    {
        return GroupOption::with('options')->find($id);
    }

    public function update(GroupOption $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(GroupOption $product): bool
    {
        return $product->delete();
    }
}