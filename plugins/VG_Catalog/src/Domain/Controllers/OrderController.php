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
use VG_Catalog\Domain\Services\OrderService;
use VG_Catalog\Domain\UseCases\Order\Actions\CreateOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\DeleteOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\GetByIdOrderAction;
use VG_Catalog\Domain\UseCases\Order\Actions\GetListOrdersAction;
use VG_Catalog\Domain\UseCases\Order\Actions\UpdateOrderAction;
use VG_Catalog\Domain\UseCases\Order\Dto\CreateOrderDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\FilterOrdersDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\OrderIdDTO;
use VG_Catalog\Domain\UseCases\Order\Dto\UpdateOrderDTO;

#[OA\Tag(
    name: 'Orders',
    description: 'Actions with order'
)]
class OrderController
{
    public function __construct(
        protected OrderService $service = new OrderService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Get(
        path: '/order-get-list',
        tags: ['Orders'],
        summary: 'Get orders list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FiltersOrdersDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Orders',
                    resourceKey: 'orders',
                    ref: '#/components/schemas/Order',
                    description: 'Orders list',
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
        $orderDTO = new FilterOrdersDTO($request);
        $this->validator->validate($orderDTO);

        $orders = $this->service->getList($orderDTO, new GetListOrdersAction());
        if ($orders instanceof LengthAwarePaginator) {
            Response::success([
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['orders' => $orders], 'getList')->json();
        }
    }

    #[OA\Get(
        path: '/order-get-by-id',
        tags: ['Orders'],
        summary: 'Get order by id',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/OrderIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Order',
                    resourceKey: 'order',
                    ref: '#/components/schemas/Order',
                    description: 'Order by id',
                    dataIsArray: false,
                    success: true,
                    action: 'getById'
                )
            ),
            new ResponseNotFound(action: 'getById', message: 'Order not found'),
            new ResponseUnprocessable(action: 'getById', message: 'Ошибка валидации')
        ]
    )]
    public function getById(mixed $request)
    {
        $orderDTO = new OrderIdDTO($request);
        $this->validator->validate($orderDTO);

        $order = $this->service->getById($orderDTO, new GetByIdOrderAction());

        Response::success(['order' => $order], 'getById')->json();
    }

    #[OA\Post(
        path: '/order-create',
        tags: ['Orders'],
        summary: 'Create order',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateOrderDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Order id',
                    ref: '#/components/schemas/OrderIdDTO',
                    description: 'Order id',
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
        $orderDTO = CreateOrderDTO::fromArray($request);

        $orderId = $this->service->create($orderDTO, new CreateOrderAction());

        Response::success(['order_id' => $orderId], 'create')->json();
    }

    #[OA\Delete(
        path: '/order-delete',
        tags: ['Orders'],
        summary: 'Delete order',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/OrderIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Order id',
                    ref: '#/components/schemas/OrderIdDTO',
                    description: 'Order id',
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseNotFound(action: 'delete', message: 'Order not found'),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function delete(mixed $request)
    {
        $orderDTO = new OrderIdDTO($request);
        $this->validator->validate($orderDTO);

        $this->service->delete($orderDTO, new DeleteOrderAction());

        Response::success(['order_id' => $orderDTO->order_id], 'delete')->json();
    }

    #[OA\Patch(
        path: '/order-update',
        tags: ['Orders'],
        summary: 'Update order',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Order id',
                    ref: '#/components/schemas/OrderIdDTO',
                    description: 'Order id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseNotFound(action: 'update', message: 'Order not found'),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function update(mixed $request)
    {
        $orderDTO = new UpdateOrderDTO($request);
        $this->validator->validate($orderDTO);

        $orderId = $this->service->update($orderDTO, new UpdateOrderAction());

        Response::success(['order_id' => $orderId], 'update')->json();
    }
}