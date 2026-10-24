<?php

namespace VG_Catalog\Domain\UseCases\Option\Actions;

use VG_Catalog\Domain\UseCases\Option\Dto\OptionIdDTO;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Option\OptionRepository;

class DeleteOptionAction
{
    public function __construct(
        protected OptionRepository $repository = new OptionRepository()
    ){}

    public function __invoke(OptionIdDTO $optionDTO): bool|\InvalidArgumentException
    {
        $option = Option::where('post_id', $optionDTO->option_id)->first();
        if (!$option) {
            throw new \InvalidArgumentException('Option not found', 404);
        }
        wp_delete_post($option->post_id, true);
        return $this->repository->delete($option);
    }
}