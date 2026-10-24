<?php

namespace VG_Catalog\Domain\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\Docs\JsonContentWithResourceKey;
use VG_Catalog\Api\Docs\ResponseOk;
use VG_Catalog\Api\Docs\ResponseUnprocessable;
use VG_Catalog\Api\Response;
use VG_Catalog\Api\Validator;
use VG_Catalog\Domain\Services\CartService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartAddDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartRemoveDTO;
use VG_Catalog\Domain\UseCases\Cart\Dto\CartUpdateDTO;

#[OA\Tag(
    name: 'Cart',
    description: 'Actions with cart'
)]
class CartController
{

    public function __construct(
        protected CartService $cartService = new CartService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Get(
        path: '/cart-get-list',
        tags: ['Cart'],
        summary: 'Get cart items',
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Cart',
                    resourceKey: 'cart',
                    ref: '#/components/schemas/Cart',
                    description: 'Cart element',
                    dataIsArray: true,
                    success: true,
                    action: 'getList'
                )
            ),
        ],
    )]
    public function getList(mixed $request)
    {
        $paginationDTO = new PaginationDTO($request);
        $this->validator->validate($paginationDTO);

        $cart = $this->cartService->getList($paginationDTO);

        if ($cart instanceof LengthAwarePaginator) {
            Response::success(['cart' => $cart->items(),
                'pagination' => [
                    'current_page' => $cart->currentPage(),
                    'last_page' => $cart->lastPage(),
                    'per_page' => $cart->perPage(),
                    'total' => $cart->total(),
                    'from' => $cart->firstItem(),
                    'to' => $cart->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['cart' => $cart], 'getList')->json();
        }
    }

    #[OA\Post(
        path: '/cart-add',
        tags: ['Cart'],
        summary: 'Add product to cart',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CartAddDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Cart id',
                    ref: '#/components/schemas/CartIdDTO',
                    description: 'Cart id',
                    dataIsArray: false,
                    success: true,
                    action: 'add'
                )
            ),
            new ResponseUnprocessable(action: 'add', message: 'Ошибка валидации')
        ]
    )]
    public function addToCart($request)
    {
        $cartAddDto = new CartAddDTO($request);
        $this->validator->validate($cartAddDto);

        $cartId = $this->cartService->addToCart($cartAddDto);

        $cartCount = $this->getCartCount();

        Response::success(['cart_id' => $cartId, 'count' => $cartCount], 'add')->json();
    }

    #[OA\Patch(
        path: '/cart-update',
        tags: ['Cart'],
        summary: 'Update cart',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CartUpdateDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Cart id',
                    ref: '#/components/schemas/CartIdDTO',
                    description: 'Cart id',
                    dataIsArray: false,
                    success: true,
                    action: 'update'
                )
            ),
            new ResponseUnprocessable(action: 'update', message: 'Ошибка валидации')
        ]
    )]
    public function updateToCart($request)
    {
        $cartUpdateDto = new CartUpdateDTO($request);
        $this->validator->validate($cartUpdateDto);

        $cartId = $this->cartService->updateToCart($cartUpdateDto);

        Response::success(['cart_id' => $cartId],'update')->json();
    }

    #[OA\Delete(
        path: '/cart-delete',
        tags: ['Cart'],
        summary: 'Update cart',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CartRemoveDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    dataIsArray: false,
                    success: true,
                    action: 'delete'
                )
            ),
            new ResponseUnprocessable(action: 'delete', message: 'Ошибка валидации')
        ]
    )]
    public function removeFromCart($request)
    {
        $cartRemoveDto = new CartRemoveDTO($request);
        $this->validator->validate($cartRemoveDto);

        $this->cartService->removeFromCart($cartRemoveDto);

        $cartCount = $this->getCartCount();

        Response::success(['count' => $cartCount], 'delete')->json();
    }

    public function getCartCount(): int
    {
        $paginationDTO = new PaginationDTO(['limit' => 0]);
        $cartList = $this->cartService->getList($paginationDTO);
        return count($cartList);
    }

    public function getCartTotal()
    {
        $total = $this->cartService->getCartTotal();
        $cartCount = $this->getCartCount();

        $fullPrice = $total['full_price'] ?? 0;
        $priceWithDiscount = $total['sale_price'] ?? 0;
        $discountAmount = 0;
        $discountPercent = 0;

        if ($fullPrice > 0 && $priceWithDiscount < $fullPrice) {
            $discountAmount = $fullPrice - $priceWithDiscount;
            $discountPercent = round(($discountAmount / $fullPrice) * 100);
        }

        $cartTotal = [
            'full_price' => $total['full_price'] ?? 0,
            'price_with_discount' => $priceWithDiscount,
            'discount_amount' => $discountAmount,
            'discount_percent' => $discountPercent,
            'count_products' => $cartCount
        ];

        Response::success($cartTotal, 'cartTotal')->json();
    }
}
