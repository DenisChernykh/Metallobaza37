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
use VG_Catalog\Domain\Services\ProductService;
use VG_Catalog\Domain\UseCases\Product\Actions\CreateProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\DeleteProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\GetListProductAction;
use VG_Catalog\Domain\UseCases\Product\Actions\GetProductByIdAction;
use VG_Catalog\Domain\UseCases\Product\Actions\UpdateProductAction;
use VG_Catalog\Domain\UseCases\Product\Dto\CreateProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\DeleteProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\FiltersProductDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\UpdateProductDTO;

#[OA\Tag(
    name: 'Products',
    description: 'Actions with products'
)]
class ProductController
{

    public function __construct(
        protected ProductService $service = new ProductService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Post(
        path: '/product-create',
        tags: ['Products'],
        summary: 'Create product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateProductDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Product',
                    resourceKey: 'product',
                    ref: '#/components/schemas/Product',
                    description: 'Created product',
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
        $productDTO = new CreateProductDTO($request);

        $this->validator->validate($productDTO);

        $product = $this->service->create($productDTO, new CreateProductAction());

        Response::success(['product' => $product], 'create')->json();
    }

    #[OA\Patch(
        path: '/product-update',
        tags: ['Products'],
        summary: 'Update product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Product id',
                    ref: '#/components/schemas/ProductIdDTO',
                    description: 'Product id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseNotFound(action: 'update', message: 'Product not found'),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function update(mixed $request)
    {
        $productDTO = new UpdateProductDTO($request);

        $this->validator->validate($productDTO);

        $this->service->update($productDTO, new UpdateProductAction());

        Response::success(['product_id' => $productDTO->product_id], 'update')->json();
    }

    #[OA\Delete(
        path: '/product-delete',
        tags: ['Products'],
        summary: 'Delete product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DeleteProductDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Product id',
                    ref: '#/components/schemas/ProductIdDTO',
                    description: 'Product id',
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseNotFound(action: 'delete', message: 'Product not found'),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function delete(mixed $request)
    {
        $productDTO = new DeleteProductDTO($request);

        $this->validator->validate($productDTO);

        $this->service->delete($productDTO, new DeleteProductAction());

        Response::success(['product_id' => $productDTO->product_id], 'delete')->json();
    }

    #[OA\Get(
        path: '/product-get-list',
        tags: ['Products'],
        summary: 'Get products list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FiltersProductDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Products',
                    resourceKey: 'products',
                    ref: '#/components/schemas/Product',
                    description: 'Products list',
                    dataIsArray: true,
                    success: true,
                    action: 'getList'
                )
            ),
            new ResponseUnprocessable(action: 'getList', message: 'Ошибка валидации')
        ]
    )]
    public function getList(mixed $request)
    {
        $filtersDTO = new FiltersProductDTO($request);

        $this->validator->validate($filtersDTO);

        $products = $this->service->getList($filtersDTO, new GetListProductAction());
        if ($products instanceof LengthAwarePaginator) {
            Response::success(['products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['products' => $products], 'getList')->json();
        }
    }

    #[OA\Get(
        path: '/product-get-by-id',
        tags: ['Products'],
        summary: 'Get product by id',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Product',
                    resourceKey: 'product',
                    ref: '#/components/schemas/Product',
                    description: 'Product by id',
                    dataIsArray: false,
                    success: true,
                    action: 'getById'
                )
            ),
            new ResponseNotFound(action: 'getById', message: 'Product not found'),
            new ResponseUnprocessable(action: 'getById', message: 'Ошибка валидации')
        ]
    )]
    public function getById(mixed $request)
    {
        $productDTO = new ProductIdDTO($request);

        $this->validator->validate($productDTO);

        $product = $this->service->getById($productDTO, new GetProductByIdAction());

        Response::success(['product' => $product], 'getById')->json();
    }
}