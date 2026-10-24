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
use VG_Catalog\Domain\Services\CategoryService;
use VG_Catalog\Domain\UseCases\Categories\Actions\CreateCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\DeleteCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetCategoryByIdAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetListCategoriesAction;
use VG_Catalog\Domain\UseCases\Categories\Actions\UpdateCategoryAction;
use VG_Catalog\Domain\UseCases\Categories\Dto\CategoryIdDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\CreateCategoryDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\FilterCategoriesDTO;
use VG_Catalog\Domain\UseCases\Categories\Dto\UpdateCategoryDTO;

#[OA\Tag(
    name: 'Categories',
    description: 'Actions with categories'
)]
class CategoriesController
{
    public function __construct(
        protected CategoryService $service = new CategoryService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Get(
        path: '/category-get-list',
        tags: ['Categories'],
        summary: 'Get list categories',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FilterCategoriesDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Category',
                    resourceKey: 'categories',
                    ref: '#/components/schemas/Category',
                    description: 'Category',
                    dataIsArray: true,
                    success: true,
                    action: 'getList'
                )
            )
        ]
    )]
    public function getList(mixed $request)
    {
        $filterDTO = new FilterCategoriesDTO($request);
        $this->validator->validate($filterDTO);

        $categories = $this->service->getList($filterDTO, new GetListCategoriesAction());
        if ($categories instanceof LengthAwarePaginator) {
            Response::success(['categories' => $categories->items(),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                ]
            ], action: 'getList')->json();
        }
        else {
            Response::success(['categories' => $categories], 'getList')->json();
        }
    }

    #[OA\Get(
        path: '/category-get-by-id',
        tags: ['Categories'],
        summary: 'Get category by id',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CategoryIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Category',
                    resourceKey: 'category',
                    ref: '#/components/schemas/Category',
                    description: 'Category',
                    dataIsArray: false,
                    success: true,
                    action: 'getById'
                )
            ),
            new ResponseNotFound(action: 'getById', message: 'Category not found'),
            new ResponseUnprocessable(action: 'getById', message: 'Ошибка валидации')
        ]
    )]
    public function getById(mixed $request)
    {
        $categoryDTO = new CategoryIdDTO($request);
        $this->validator->validate($categoryDTO);

        $category = $this->service->getById($categoryDTO, new GetCategoryByIdAction());
        Response::success(['category' => $category], action: 'getById')->json();
    }

    #[OA\Post(
        path: '/category-create',
        tags: ['Categories'],
        summary: 'Create category',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCategoryDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Category id',
                    ref: '#/components/schemas/CategoryIdDTO',
                    description: 'Category id',
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
        $categoryDTO = new CreateCategoryDTO($request);
        $this->validator->validate($categoryDTO);

        $category = $this->service->create($categoryDTO, new CreateCategoryAction());
        Response::success(['category_id' => $category], action: 'create')->json();
    }

    #[OA\Patch(
        path: '/category-update',
        tags: ['Categories'],
        summary: 'Update category',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCategoryDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Category id',
                    ref: '#/components/schemas/CategoryIdDTO',
                    description: 'Category id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseNotFound(action: 'update', message: 'Category not found'),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function update(mixed $request)
    {
        $categoryDTO = new UpdateCategoryDTO($request);
        $this->validator->validate($categoryDTO);

        $this->service->update($categoryDTO, new UpdateCategoryAction());
        Response::success(['category_id' => $categoryDTO->category_id], action: 'update')->json();
    }

    #[OA\Delete(
        path: '/category-delete',
        tags: ['Categories'],
        summary: 'Delete category',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CategoryIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Category id',
                    ref: '#/components/schemas/CategoryIdDTO',
                    description: 'Category id',
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseNotFound(action: 'delete', message: 'Category not found'),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function delete(mixed $request)
    {
        $categoryDTO = new CategoryIdDTO($request);
        $this->validator->validate($categoryDTO);

        $this->service->delete($categoryDTO, new DeleteCategoryAction());
        Response::success(['category_id' => $categoryDTO->category_id], action: 'delete')->json();
    }

}