<?php

namespace VG_Mailer\src;

use Exception;
use VG_Mailer\src\interfaces\MiddlewareInterface;

class Router
{

    private static array $routes = [];

    public static function add(Route $route): void
    {
        self::$routes[] = $route;
    }

    public static function dispatch(): void
    {
        foreach (self::$routes as $route) {
            $data = $route->getData();
            $action = $route->getAction();
            $handler = $route->getHandler();
            $middlewares = $route->getMiddlewares();

            if (($data['action'] ?? null) !== $action && !isset($data[$action])) {
                continue;
            }

            if (is_array($middlewares)) {
                foreach ($middlewares as $middleware) {
                    $response = self::executeMiddleware($middleware);
                    if ($response->isError()) {
                        $response->json();
                    }
                }
            }

            self::executeHandler($handler, $data);
        }
    }

    private static function executeMiddleware(?string $middlewareClass): Response
    {
        if ($middlewareClass === null) {
            return Response::success();
        }

        if (!class_exists($middlewareClass)) {
            return Response::error(
                "Middleware not found: $middlewareClass",
                500
            );
        }

        $middleware = new $middlewareClass();
        if (!$middleware instanceof MiddlewareInterface) {
            return Response::error("Invalid middleware: $middlewareClass", 500);
        }

        return $middleware->handle();
    }

    private static function executeHandler(array $handler, array $data): void
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
            Response::error($message, 500)->json();
        }
    }

}