<?php

namespace VG_Catalog\Domain\UseCases\Option\Actions;

use VG_Catalog\Domain\UseCases\Option\Dto\UpdateOptionDTO;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Option\OptionRepository;

class UpdateOptionAction
{
    public function __construct(
        protected OptionRepository $repository = new OptionRepository()
    ){}

    public function __invoke(UpdateOptionDTO $optionDTO)
    {
        $option = Option::where('post_id', $optionDTO->option_id)->first();
        if (!$option) {
            throw new \InvalidArgumentException('Option not found', 404);
        }

        $post = get_post($option->post_id);
        $post->post_title = $optionDTO->title;
        wp_insert_post($post->to_array());

        return $this->repository->update($option, [
            'additional' => json_encode($optionDTO->additional, true),
        ]);
    }
}