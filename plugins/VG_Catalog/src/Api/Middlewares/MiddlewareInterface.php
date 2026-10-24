<?php

namespace VG_Catalog\Api\Middlewares;

use VG_Catalog\Api\Response;

interface MiddlewareInterface
{

    public function handle(): Response;

}