<?php

namespace VG_Mailer\src\middlewares;

use VG_Mailer\src\cms\Wordpress;
use VG_Mailer\src\interfaces\MiddlewareInterface;
use VG_Mailer\src\Response;

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