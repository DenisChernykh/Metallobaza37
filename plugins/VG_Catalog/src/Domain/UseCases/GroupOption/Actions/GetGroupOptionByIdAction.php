<?php

namespace VG_Catalog\Domain\UseCases\GroupOption\Actions;

use VG_Catalog\Domain\UseCases\GroupOption\Dto\GroupOptionIdDTO;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOptionRepository;

class GetGroupOptionByIdAction
{
    public function __construct(
        protected GroupOptionRepository $repository = new GroupOptionRepository()
    ){}

    public function __invoke(GroupOptionIdDTO $groupOptionIdDTO): GroupOption|\InvalidArgumentException
    {
        $group = GroupOption::where('post_id', $groupOptionIdDTO->group_option_id)->first();
        if (!$group) {
            throw new \InvalidArgumentException('Group options not found', 404);
        }
        return $group;
    }
}