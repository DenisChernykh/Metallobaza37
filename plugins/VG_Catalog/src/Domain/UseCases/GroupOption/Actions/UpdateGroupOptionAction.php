<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Actions;

use VG_Catalog\Domain\UseCases\GroupOption\Dto\UpdateGroupOptionDTO;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOptionRepository;

class UpdateGroupOptionAction
{
    public function __construct(
        protected GroupOptionRepository $repository = new GroupOptionRepository()
    ){}

    public function __invoke(UpdateGroupOptionDTO $groupOptionDTO): bool|\InvalidArgumentException
    {
        $group = GroupOption::where('post_id', $groupOptionDTO->group_option_id)->first();
        if (!$group) {
            throw new \InvalidArgumentException('Group options not found', 404);
        }

        $post = get_post($group->post_id);
        $post->post_title = $groupOptionDTO->title;
        wp_insert_post($post->to_array());

        return $this->repository->update($group, [
            'required' => $groupOptionDTO->required ? 'yes' : '',
            'multiple' => $groupOptionDTO->multiple ? 'yes' : '',
        ]);
    }
}