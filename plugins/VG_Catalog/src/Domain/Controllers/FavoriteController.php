<?php

namespace VG_Catalog\Domain\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;
use VG_Catalog\Api\Docs\JsonContentMessage;
use VG_Catalog\Api\Docs\JsonContentWithResourceKey;
use VG_Catalog\Api\Docs\ResponseOk;
use VG_Catalog\Api\Docs\ResponseUnprocessable;
use VG_Catalog\Api\Response;
use VG_Catalog\Api\Validator;
use VG_Catalog\Domain\Services\FavoriteService;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\UseCases\Product\Dto\ProductIdDTO;

#[OA\Tag(
    name: 'Favorites',
    description: 'Actions with favorites'
)]
class FavoriteController
{

    public function __construct(
        protected FavoriteService $service = new FavoriteService(),
        protected Validator $validator = new Validator()
    ){}

    #[OA\Post(
        path: '/favorite-toggle',
        tags: ['Favorites'],
        summary: 'Toggle favorite',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentMessage(
                    title: 'Toggle favorite',
                    success: true,
                    action: 'addToFavorite',
                )
            ),
            new ResponseUnprocessable(action: 'toggleFavorite', message: 'Ошибка валидации')
        ]
    )]
    public function toggleFavorite(mixed $request)
    {
        $toggleFavoriteDTO = new ProductIdDTO([
            'product_id' => $request['product_id'],
        ]);

        $this->validator->validate($toggleFavoriteDTO);

        $result = $this->service->toggleFavorite($toggleFavoriteDTO);

        $favoritesCount = $this->getFavoriteCount();

        Response::success(['count' => $favoritesCount], action: $result)->json();
    }

    #[OA\Get(
        path: '/favorite-check',
        tags: ['Favorites'],
        summary: 'Check is favorite',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductIdDTO')
        ),
        responses: [
            new ResponseOk(
                content: new JsonContentMessage(
                    title: 'Is favorited',
                    success: true,
                    action: 'isFavorite',
                    message: true,
                    type: 'boolean',
                    resourceKey: 'isFavorite'
                )
            ),
            new ResponseUnprocessable(action: 'isFavorite', message: 'Ошибка валидации')
        ]
    )]
    public function isFavorite(mixed $request)
    {
        $toggleFavoriteDTO = new ProductIdDTO([
            'product_id' => $request['product_id'],
        ]);

        $this->validator->validate($toggleFavoriteDTO);

        $result = $this->service->isFavorite($toggleFavoriteDTO);

        Response::success(['isFavorite' => $result], 'isFavorite')->json();
    }

    #[OA\Get(
        path: '/favorite-get-list',
        tags: ['Favorites'],
        summary: 'Get favorites list',
        responses: [
            new ResponseOk(
                content: new JsonContentWithResourceKey(
                    title: 'Products',
                    resourceKey: 'favorites',
                    ref: '#/components/schemas/Product',
                    description: 'Favorites list',
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

        $favorites = $this->service->getList($paginationDTO);
        if ($favorites instanceof LengthAwarePaginator) {
            Response::success(['favorites' => $favorites->items(),
                'pagination' => [
                    'current_page' => $favorites->currentPage(),
                    'last_page' => $favorites->lastPage(),
                    'per_page' => $favorites->perPage(),
                    'total' => $favorites->total(),
                    'from' => $favorites->firstItem(),
                    'to' => $favorites->lastItem(),
                ]
            ], 'getList')->json();
        }
        else {
            Response::success(['favorites' => $favorites], 'getList')->json();
        }
    }

    public function getFavoriteCount(): int
    {
        $paginationDTO = new PaginationDTO(['limit' => 0]);
        $favorites = $this->service->getList($paginationDTO);
        return count($favorites);
    }
}