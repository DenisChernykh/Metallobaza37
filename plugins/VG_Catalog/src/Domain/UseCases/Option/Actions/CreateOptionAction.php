<?php

namespace VG_Catalog\Domain\UseCases\Option\Actions;

use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOptionRepository;
use VG_Catalog\Domain\UseCases\Option\Dto\CreateOptionDTO;
use VG_Catalog\Domain\UseCases\Option\Option;
use VG_Catalog\Domain\UseCases\Option\OptionRepository;

class CreateOptionAction
{
    public function __construct(
        protected OptionRepository $repository = new OptionRepository(),
        protected GroupOptionRepository $groupRepository = new GroupOptionRepository()
    ){}

    public function __invoke(CreateOptionDTO $optionDTO): Option|\InvalidArgumentException
    {
        $group = GroupOption::where('post_id', $optionDTO->group_id)->first();

        if (!$group) {
            throw new \InvalidArgumentException('Group not found', 404);
        }
        $postId = wp_insert_post([
            'post_title' => $optionDTO->title,
            'post_status' => 'publish',
            'post_type' => 'vg_options',
        ]);

        return $this->repository->create([
            'post_id' => $postId,
            'group_id' => $group->post_id,
            'additional' => json_encode($optionDTO->additional, true),
        ]);
    }
}