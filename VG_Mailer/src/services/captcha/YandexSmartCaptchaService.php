<?php

namespace VG_Mailer\src\services\captcha;

use Exception;
use VG_Mailer\src\Core;
use VG_Mailer\src\interfaces\CaptchaItemInterface;
use VG_Mailer\src\interfaces\CaptchaItemServiceInterface;
use VG_Mailer\src\logs\enums\JsLogEnum;
use VG_Mailer\src\logs\JsLog;
use VG_Mailer\src\models\YandexSmartCaptchaModel;
use VG_Mailer\src\services\form\FormService;

class YandexSmartCaptchaService implements CaptchaItemServiceInterface
{
    private FormService $formService;

    public function __construct()
    {
        $this->formService = new FormService();
    }

    public function getScriptSrc(CaptchaItemInterface $model): ?string
    {
        return $this->checkSettings($model) ? 'https://smartcaptcha.yandexcloud.net/captcha.js' : null;
    }

    public function checkSettings(CaptchaItemInterface $model): bool
    {
        if (!$model->getClientKey() || !$model->getServerKey()) {
            $message = sprintf('Установите настройки для %s', $model->getModelName());
            if (Core::make()->isDebug()) {
                $model->setUse(false);
                JsLog::addLog(JsLogEnum::ERROR, $message);
            } else {
                throw new Exception($message);
            }
            return false;
        }
        return true;
    }

    public function checkCaptcha(array $data): bool
    {
        $yandexModel = YandexSmartCaptchaModel::make();
        $formKey = $this->formService->getFormValueByKey('smart-token', $data);

        if ($formKey->isError()) {
            return false;
        }

        $ch = curl_init();
        $args = http_build_query([
            "secret" => $yandexModel->getServerKey(),
            "token" => $formKey->getData(),
            "ip" => $_SERVER['REMOTE_ADDR'],
        ]);
        curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            return false;
        }

        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }
}
