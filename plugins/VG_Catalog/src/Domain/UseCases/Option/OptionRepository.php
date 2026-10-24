<?php

namespace VG_Catalog\Domain\UseCases\Option;

class OptionRepository
{
    public function create(array $product): Option
    {
        return Option::create($product);
    }

    public function getById($id): Option|null
    {
        return Option::with('group')->find($id);
    }

    public function update(Option $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Option $product): bool
    {
        return $product->delete();
    }
}