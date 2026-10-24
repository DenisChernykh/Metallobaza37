<?php

namespace VG_Mailer\src\middlewares;

use VG_Mailer\src\interfaces\MiddlewareInterface;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\db\DBWorker;

class DatabaseMiddleware implements MiddlewareInterface
{

    public function handle(): Response
    {
        return DBWorker::isCanUse() ? Response::success() : Response::error('Database connection failed');
    }
}