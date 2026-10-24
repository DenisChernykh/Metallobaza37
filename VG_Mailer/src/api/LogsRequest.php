<?php

namespace VG_Mailer\src\api;

use VG_Mailer\src\Core;
use VG_Mailer\src\logs\JsLog;
use VG_Mailer\src\Response;

class LogsRequest
{
    public function getData()
    {
        $core = Core::make();
        Response::success(JsLog::getSessionData())->json();
    }
}