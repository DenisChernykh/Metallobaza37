<?php

namespace VG_Mailer\src\interfaces;

use VG_Mailer\src\Response;

interface MiddlewareInterface
{

    public function handle(): Response;

}