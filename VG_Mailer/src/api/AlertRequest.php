<?php

namespace VG_Mailer\src\api;

use VG_Mailer\src\models\AlertModel;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\AlertService;

class AlertRequest
{
    private AlertService $alertService;

    public function __construct()
    {
        $this->alertService = new AlertService();
    }

    public function getVGAlertData()
    {
        Response::success(AlertModel::make()->toArray())->json();
    }

    public function getVGAlertScript()
    {
        $this->alertService->getVGAlertScript();
    }
}