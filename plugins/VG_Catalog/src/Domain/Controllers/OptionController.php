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
use VG_Catalog\Domain\Services\OptionService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Option\Actions\CreateOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\DeleteOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\GetListOptionAction;
use VG_Catalog\Domain\UseCases\Option\Actions\GetOptionByIdAction;
use VG_Catalog\Domain\UseCases\Option\Actions\UpdateOptionAction;
use VG_Catalog\Domain\UseCases\Option\Dto\CreateOptionDTO;
use VG_Catalog\Domain\UseCases\Option\Dto\OptionIdDTO;
use VG_Catalog\Domain\UseCases\Option\Dto\UpdateOptionDTO;

#[OA\Tag(
    name: 'Options',
    description: 'Actions with option'
)]
class OptionController
{

    public function __construct(
        protected OptionService $service = new OptionService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Post(
        path: '/option-create',
        tags: ['Options'],
        summary: 'Create option',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateOptionDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Option',
                    resourceKey: 'option',
                    ref: '#/components/schemas/Option',
                    description: 'Created option',
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
        $optionDTO = new CreateOptionDTO($request);
        $this->validator->validate($optionDTO);

        $option = $this->service->create($optionDTO, new CreateOptionAction());

        Response::success(['option' => $option], 'create')->json();
    }

    #[OA\Get(
        path: '/option-get-list',
        tags: ['Options'],
        summary: 'Get options list',
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Options',
                    resourceKey: 'options',
                    ref: '#/components/schemas/Option',
                    description: 'Options list',
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

        $options = $this->service->getList($paginationDTO, new GetListOptionAction());
        if ($options instanceof LengthAwarePaginator) {
            Response::success(['options' => $options->items(),
                'pagination' => [
                    'current_page' => $options->currentPage(),
                    'last_page' => $options->lastPage(),
                    'per_page' => $options->perPage(),
                    'total' => $options->total(),
                    'from' => $options->firstItem(),
                    'to' => $options->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['options' => $options], 'getList')->json();
        }
    }

    #[OA\Get(
        path: '/option-get-by-id',
        tags: ['Options'],
        summary: 'Get option by id',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/OptionIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Option',
                    resourceKey: 'option',
                    ref: '#/components/schemas/Product',
                    description: 'Option by id',
                    dataIsArray: false,
                    success: true,
                    action: 'getById'
                )
            ),
            new ResponseNotFound(action: 'getById', message: 'Option not found'),
            new ResponseUnprocessable(action: 'getById', message: 'Ошибка валидации')
        ]
    )]
    public function getById(mixed $request)
    {
        $optionDTO = new OptionIdDTO($request);
        $this->validator->validate($optionDTO);

        $option = $this->service->getById($optionDTO, new GetOptionByIdAction());

        Response::success(['option' => $option], 'getById')->json();
    }

    #[OA\Patch(
        path: '/option-update',
        tags: ['Options'],
        summary: 'Update option',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOptionDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Option id',
                    ref: '#/components/schemas/OptionIdDTO',
                    description: 'Option id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseNotFound(action: 'update', message: 'Option not found'),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function update(mixed $request)
    {
        $optionDTO = new UpdateOptionDTO($request);
        $this->validator->validate($optionDTO);

        $this->service->update($optionDTO, new UpdateOptionAction());

        Response::success(['option_id' => $optionDTO->option_id], 'update')->json();
    }

    #[OA\Delete(
        path: '/option-delete',
        tags: ['Options'],
        summary: 'Delete option',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/OptionIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Option id',
                    ref: '#/components/schemas/OptionIdDTO',
                    description: 'Option id',
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseNotFound(action: 'delete', message: 'Option not found'),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function delete(mixed $request)
    {
        $optionDTO = new OptionIdDTO($request);
        $this->validator->validate($optionDTO);

        $this->service->delete($optionDTO, new DeleteOptionAction());

        Response::success(['option_id' => $optionDTO->option_id], 'delete')->json();
    }
}