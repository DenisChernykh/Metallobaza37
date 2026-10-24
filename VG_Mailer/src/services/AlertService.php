<?php

namespace VG_Mailer\src\services;

use VG_Mailer\src\Core;
use VG_Mailer\src\Response;

class AlertService
{
    public function getVGAlertScript(): void
    {
        $httpPath = Core::make()->getModulePath();
        $fullPath = Core::make()->getModulePath(true);
        $scriptPath = '/resources/assets/js/admin/vgalerts.js';

        if (!file_exists($fullPath.$scriptPath)) {
            Response::error('VG_Alert script not found')->json();
        }

        Response::success($httpPath.$scriptPath)->json();
    }
}