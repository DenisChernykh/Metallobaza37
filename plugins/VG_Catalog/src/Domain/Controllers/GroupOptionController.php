<?php

namespace VG_Catalog\Domain\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\Docs\JsonContentWithResourceKey;
use VG_Catalog\Api\Docs\ResponseNotFound;
use VG_Catalog\Api\Docs\ResponseOk;
use VG_Catalog\Api\Docs\ResponseUnprocessable;
use VG_Catalog\Api\Response;
use VG_Catalog\Api\Validator;
use VG_Catalog\Domain\Services\GroupOptionService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\CreateGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\DeleteGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\GetGroupOptionByIdAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\GetListGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Actions\UpdateGroupOptionAction;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\CreateGroupOptionDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\GroupOptionIdDTO;
use VG_Catalog\Domain\UseCases\GroupOption\Dto\UpdateGroupOptionDTO;

#[OA\Tag(
    name: 'Group options',
    description: 'Actions with group options'
)]
class GroupOptionController
{

    public function __construct(
        protected GroupOptionService $service = new GroupOptionService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Post(
        path: '/group-options-create',
        tags: ['Group options'],
        summary: 'Create group options',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateGroupOptionDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Option',
                    resourceKey: 'option',
                    ref: '#/components/schemas/GroupOption',
                    description: 'Created group options',
                    dataIsArray: false,
                    success: true,
                    action: 'create'
                )
            ),
            new ResponseUnprocessable(action: 'create', message: 'Ошибка валидации')
        ]
    )]
    public function create(mixed $request)
    {
        $groupOptionDTO = new CreateGroupOptionDTO($request);
        $this->validator->validate($groupOptionDTO);

        $group = $this->service->create($groupOptionDTO, new CreateGroupOptionAction());

        Response::success(['group' => $group], 'create')->json();
    }

    #[OA\Get(
        path: '/group-options-get-list',
        tags: ['Group options'],
        summary: 'Get groups options list',
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Group options',
                    resourceKey: 'group',
                    ref: '#/components/schemas/GroupOption',
                    description: 'Group options list',
                    dataIsArray: true,
                    success: true,
                    action: 'getList'
                )
            )
        ]
    )]
    public function getList(mixed $request)
    {
        $paginationDTO = new PaginationDTO($request);
        $this->validator->validate($paginationDTO);

        $groups = $this->service->getList($paginationDTO, new GetListGroupOptionAction());
        if ($groups instanceof LengthAwarePaginator) {
            Response::success(['groups' => $groups->items(),
                'pagination' => [
                    'current_page' => $groups->currentPage(),
                    'last_page' => $groups->lastPage(),
                    'per_page' => $groups->perPage(),
                    'total' => $groups->total(),
                    'from' => $groups->firstItem(),
                    'to' => $groups->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['groups' => $groups], 'getList')->json();
        }
    }

    #[OA\Get(
        path: '/group-options-get-by-id',
        tags: ['Group options'],
        summary: 'Get group options by id',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GroupOptionIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Group options',
                    resourceKey: 'group',
                    ref: '#/components/schemas/GroupOption',
                    description: 'Group option by id',
                    dataIsArray: false,
                    success: true,
                    action: 'getById'
                )
            ),
            new ResponseNotFound(action: 'getById', message: 'Group option not found'),
            new ResponseUnprocessable(action: 'getById', message: 'Ошибка валидации')
        ]
    )]
    public function getById(mixed $request)
    {
        $groupOptionDTO = new GroupOptionIdDTO($request);
        $this->validator->validate($groupOptionDTO);

        $group = $this->service->getById($groupOptionDTO, new GetGroupOptionByIdAction());

        Response::success(['group' => $group], 'getById')->json();
    }

    #[OA\Patch(
        path: '/group-options-update',
        tags: ['Group options'],
        summary: 'Update group options',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateGroupOptionDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Group option id',
                    ref: '#/components/schemas/GroupOptionIdDTO',
                    description: 'Group option id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseNotFound(action: 'update', message: 'Group option not found'),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function update(mixed $request)
    {
        $groupOptionDTO = new UpdateGroupOptionDTO($request);
        $this->validator->validate($groupOptionDTO);

        $this->service->update($groupOptionDTO, new UpdateGroupOptionAction());

        Response::success(['group_id' => $groupOptionDTO->group_option_id], 'update')->json();
    }

    #[OA\Delete(
        path: '/group-options-delete',
        tags: ['Group options'],
        summary: 'Delete group options',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GroupOptionIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Group option id',
                    ref: '#/components/schemas/GroupOptionIdDTO',
                    description: 'Group option id',
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseNotFound(action: 'delete', message: 'Group option not found'),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function delete(mixed $request)
    {
        $groupOptionDTO = new GroupOptionIdDTO($request);
        $this->validator->validate($groupOptionDTO);

        $this->service->delete($groupOptionDTO, new DeleteGroupOptionAction());

        Response::success(['group_id' => $groupOptionDTO->group_option_id], 'delete')->json();
    }
}