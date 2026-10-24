<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Actions;

use VG_Catalog\Domain\UseCases\GroupOption\Dto\CreateGroupOptionDTO;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOptionRepository;

class CreateGroupOptionAction
{
    public function __construct(
        protected GroupOptionRepository $repository = new GroupOptionRepository()
    ){}

    public function __invoke(CreateGroupOptionDTO $groupOptionDTO): GroupOption
    {
        $postId = wp_insert_post([
            'post_title' => $groupOptionDTO->title,
            'post_status' => 'publish',
            'post_type' => 'vg_group_options',
        ]);

        return $this->repository->create([
            'post_id' => $postId,
            'required' => $groupOptionDTO->required ? 'yes' : '',
            'multiple' => $groupOptionDTO->multiple ? 'yes' : '',
        ]);
    }
}