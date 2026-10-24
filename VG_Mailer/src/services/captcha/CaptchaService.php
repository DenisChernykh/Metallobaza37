<?php

namespace VG_Mailer\src\services\captcha;

use VG_Mailer\src\models\CaptchaModel;
use VG_Mailer\src\Response;
use VG_Mailer\src\services\form\FormService;

class CaptchaService
{
    private FormService $formService;

    public function __construct()
    {
        $this->formService = new FormService();
    }

    public function getData(): Response
    {
        $captchaModel = CaptchaModel::make();
        $captchaModels = $captchaModel->getModels();

        $json = [
            'status' => $captchaModel->isUse(),
            'data' => []
        ];


        if (!$captchaModel->isUse()) {
            Response::success($json);
        }

        foreach ($captchaModels as $model) {
            if (!$model->isUse()) {
                continue;
            }

            $json['data'][] = [
                'name' => $model->getModelName(),
                'key' => $model->getModelKey(),
                'client_key' => $model->getClientKey()
            ];
        }

        return Response::success($json);
    }

    public function checkCaptcha(array &$data): Response
    {
        $captchaModel = CaptchaModel::make();
        if (!$captchaModel->isUse()) {
            Response::success();
        }

        $captchaModels = $captchaModel->getModels();
        foreach ($captchaModels as $model) {
            if (!$model->isUse()) {
                continue;
            }

            $service = $model->getService();
            if (!$service->checkCaptcha($data)) {
                return Response::error('Капча не пройдена');
            } else {
                $this->formService->deleteFormValueByKey($model->getModelPostKey(), $data);
            }
        }

        return Response::success();
    }
}