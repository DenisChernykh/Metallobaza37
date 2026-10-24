<?php

namespace VG_Catalog\Api\Routing;

use Exception;
use VG_Catalog\Api\Response;
use VG_Catalog\Api\Middlewares\MiddlewareInterface;

class Router
{

    private static array $routes = [];

    public static function add($method, $path, Route $route): void
    {
        self::$routes[$method][$path] = $route;
    }

    public static function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $input = file_get_contents('php://input');
        $request = json_decode($input, true) ?? [];

        if ($method === 'GET' && isset($_GET['route'])) {
            $request['route'] = $_GET['route'];
            if (isset($_GET['limit'])) {
                $request['limit'] = $_GET['limit'];
            }
        }
        $path = $request['route'] ?? '';

        $route = self::$routes[$method][$path] ?? null;
        if (!$route) {
            Response::error("Route not found", $path, 404)->json();
        }
        $data = $request;
        $handler = $route->getHandler();
        $middlewares = $route->getMiddlewares();

        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                $response = self::executeMiddleware($middleware);
                if ($response->isError()) {
                    $response->json();
                }
            }
        }

        self::executeHandler($handler, $data, $path);

    }

    private static function executeMiddleware(?string $middlewareClass): Response
    {
        if ($middlewareClass === null) {
            Response::success()->json();
        }

        if (!class_exists($middlewareClass)) {
            Response::error("Middleware not found: $middlewareClass")->json();
        }

        $middleware = new $middlewareClass();
        if (!$middleware instanceof MiddlewareInterface) {
            Response::error("Invalid middleware: $middlewareClass")->json();
        }

        return $middleware->handle();
    }

    private static function executeHandler(array $handler, array $data, string $action): void
    {
        [$className, $methodName] = $handler;

        if (!class_exists($className) || !method_exists(
                $className,
                $methodName
            )) {
            Response::error('Handler not found', 500)->json();
            return;
        }

        try {
            $controller = new $className();
            $controller->$methodName($data, $_FILES);
        } catch (Exception $e) {
            $message = $e->getMessage();

            Response::error($message, $action, is_int($e->getCode()) ? $e->getCode() : 500)->json();
        }
    }
}