<?php

namespace VG_Mailer\src\models;

use VG_Mailer\src\interfaces\CaptchaItemInterface;
use VG_Mailer\src\services\captcha\YandexSmartCaptchaService;

class YandexSmartCaptchaModel implements CaptchaItemInterface
{
    private static YandexSmartCaptchaModel $model;

    private string $model_name = 'Яндекс: SmartCaptcha';
    private string $model_key = 'yandex_smart_captcha';
    private bool $use = false;
    private string $client_key = '';
    private string $server_key = '';
    private string $post_key = 'smart-token';

    public static function make(): YandexSmartCaptchaModel
    {
        if (!isset(self::$model)) {
            self::$model = new YandexSmartCaptchaModel();
        }
        return self::$model;
    }

    public function getService(): YandexSmartCaptchaService
    {
        return new YandexSmartCaptchaService();
    }

    public function getModelName(): string
    {
        return $this->model_name;
    }

    public function getModelKey(): string
    {
        return $this->model_key;
    }

    public function isUse(): bool
    {
        return $this->use;
    }

    public function setUse(bool $use): YandexSmartCaptchaModel
    {
        $this->use = $use;
        return $this;
    }

    public function getClientKey(): string
    {
        return $this->client_key;
    }

    public function setClientKey(string $client_key): YandexSmartCaptchaModel
    {
        $this->client_key = $client_key;
        return $this;
    }

    public function getServerKey(): string
    {
        return $this->server_key;
    }

    public function setServerKey(string $server_key): YandexSmartCaptchaModel
    {
        $this->server_key = $server_key;
        return $this;
    }

    public function getModelPostKey(): string
    {
        return $this->post_key;
    }
}