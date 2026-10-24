<?php

namespace VG_Catalog\Api;

use OpenApi\Attributes as OA;
use VG_Catalog\Api\Routing\Route;
use VG_Catalog\Api\Routing\Router;

#[OA\Info(
    title: "VG Cart API",
    version: "1.0"
)]
class Api
{
    public static function post(string $action, array $handler): Route
    {
        $route = new Route($handler);
        Router::add('POST', $action, $route);
        return $route;
    }

    public static function get(string $action, array $handler): Route
    {
        $route = new Route($handler);
        Router::add('GET', $action, $route);
        return $route;
    }

    public static function delete(string $action, array $handler): Route
    {
        $route = new Route($handler);
        Router::add('DELETE', $action, $route);
        return $route;
    }

    public static function patch(string $action, array $handler): Route
    {
        $route = new Route($handler);
        Router::add('PATCH', $action, $route);
        return $route;
    }

    public static function put(string $action, array $handler): Route
    {
        $route = new Route($handler);
        Router::add('PUT', $action, $route);
        return $route;
    }
}
