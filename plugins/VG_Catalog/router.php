<?php

use VG_Catalog\Api\Api;
use VG_Catalog\Api\Middlewares\AuthMiddleware;
use VG_Catalog\Api\Routing\Router;
use VG_Catalog\Database;
use VG_Catalog\Domain\Controllers\CartController;
use VG_Catalog\Domain\Controllers\CategoriesController;
use VG_Catalog\Domain\Controllers\FavoriteController;
use VG_Catalog\Domain\Controllers\GroupOptionController;
use VG_Catalog\Domain\Controllers\OptionController;
use VG_Catalog\Domain\Controllers\OrderController;
use VG_Catalog\Domain\Controllers\ProductController;

require_once __DIR__ . '/vendor/autoload.php';

Database::init();

Api::get('cart-get-list', [CartController::class, 'getList']);
Api::post('cart-add', [CartController::class, 'addToCart']);
Api::patch('cart-update', [CartController::class, 'updateToCart']);
Api::delete('cart-remove', [CartController::class, 'removeFromCart']);
Api::get('cart-get-total', [CartController::class, 'getCartTotal']);

Api::post('product-create', [ProductController::class, 'create'])->middleware(AuthMiddleware::class);
Api::patch('product-update', [ProductController::class, 'update'])->middleware(AuthMiddleware::class);
Api::delete('product-delete', [ProductController::class, 'delete'])->middleware(AuthMiddleware::class);
Api::get('product-get-list', [ProductController::class, 'getList']);
Api::get('product-get-by-id', [ProductController::class, 'getById']);

Api::post('favorite-toggle', [FavoriteController::class, 'toggleFavorite']);
Api::get('favorite-check', [FavoriteController::class, 'isFavorite']);
Api::get('favorite-get-list', [FavoriteController::class, 'getList']);

Api::get('group-options-get-list', [GroupOptionController::class, 'getList']);
Api::get('group-options-get-by-id', [GroupOptionController::class, 'getById']);
Api::post('group-options-create', [GroupOptionController::class, 'create'])->middleware(AuthMiddleware::class);
Api::patch('group-options-update', [GroupOptionController::class, 'update'])->middleware(AuthMiddleware::class);
Api::delete('group-options-delete', [GroupOptionController::class, 'delete'])->middleware(AuthMiddleware::class);

Api::get('option-get-list', [OptionController::class, 'getList']);
Api::get('option-get-by-id', [OptionController::class, 'getById']);
Api::post('option-create', [OptionController::class, 'create'])->middleware(AuthMiddleware::class);
Api::patch('option-update', [OptionController::class, 'update'])->middleware(AuthMiddleware::class);
Api::delete('option-delete', [OptionController::class, 'delete'])->middleware(AuthMiddleware::class);

Api::get('category-get-list', [CategoriesController::class, 'getList']);
Api::get('category-get-by-id', [CategoriesController::class, 'getById']);
Api::post('category-create', [CategoriesController::class, 'create'])->middleware(AuthMiddleware::class);
Api::patch('category-update', [CategoriesController::class, 'update'])->middleware(AuthMiddleware::class);
Api::delete('category-delete', [CategoriesController::class, 'delete'])->middleware(AuthMiddleware::class);

Api::get('order-get-list', [OrderController::class, 'getList'])->middleware(AuthMiddleware::class);
Api::get('order-get-by-id', [OrderController::class, 'getById'])->middleware(AuthMiddleware::class);
Api::post('order-create', [OrderController::class, 'create']);
Api::patch('order-update', [OrderController::class, 'update'])->middleware(AuthMiddleware::class);
Api::delete('order-delete', [OrderController::class, 'delete'])->middleware(AuthMiddleware::class);

Router::dispatch();
 