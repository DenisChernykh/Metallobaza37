<?php

namespace VG_Mailer\src;

class Api
{

    /**
     * @param string $action
     * @param array $handler
     * @return Route
     */
    public static function get(string $action, array $handler): Route
    {
        $route = new Route($_GET, $action, $handler);
        Router::add($route);
        return $route;
    }

    /**
     * @param string $action
     * @param array $handler
     * @return Route
     */
    public static function post(string $action, array $handler): Route
    {
        $route = new Route($_POST, $action, $handler);
        Router::add($route);
        return $route;
    }

}