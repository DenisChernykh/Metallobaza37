<?php

namespace VG_Mailer\src\services\captcha;

use Exception;
use VG_Mailer\src\Core;
use VG_Mailer\src\interfaces\CaptchaItemInterface;
use VG_Mailer\src\interfaces\CaptchaItemServiceInterface;
use VG_Mailer\src\logs\enums\JsLogEnum;
use VG_Mailer\src\logs\JsLog;
use VG_Mailer\src\models\CloudflareCaptchaModel;
use VG_Mailer\src\services\form\FormService;

class CloudflareCaptchaService implements CaptchaItemServiceInterface
{
    private FormService $formService;

    public function __construct()
    {
        $this->formService = new FormService();
    }

    public function getScriptSrc(CaptchaItemInterface $model): ?string
    {
        return $this->checkSettings($model) ? 'https://challenges.cloudflare.com/turnstile/v0/api.js' : null;
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

    public function checkCaptcha($data): bool
    {
        $cloudflareModel = CloudflareCaptchaModel::make();
        $formKey = $this->formService->getFormValueByKey('cf-turnstile-response', $data);

        if ($formKey->isError()) {
            return false;
        }

        $ch = curl_init();
        $args = http_build_query([
            "secret" => $cloudflareModel->getServerKey(),
            "response" => $formKey->getData(),
            "remoteip" => $_SERVER['REMOTE_ADDR'],
        ]);
        curl_setopt($ch, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        $resp = json_decode($server_output, true);

        return $resp['success'] ?? false;
    }
}
