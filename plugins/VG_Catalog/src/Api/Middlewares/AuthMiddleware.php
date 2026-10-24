<?php

namespace VG_Catalog\Api\Middlewares;

use VG_Catalog\Api\Response;
use VG_Catalog\Api\Wordpress;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle(): Response
    {
        $wordpress = Wordpress::make();

        if ($wordpress->isWp()) {
            return $wordpress->isAdmin() ? Response::success() : Response::error('Forbidden Wordpress');
        }

        return Response::success();
    }
}