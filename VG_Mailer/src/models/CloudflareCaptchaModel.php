<?php

namespace VG_Mailer\src\models;

use VG_Mailer\src\interfaces\CaptchaItemInterface;
use VG_Mailer\src\services\captcha\CloudflareCaptchaService;

class CloudflareCaptchaModel implements CaptchaItemInterface
{
    private static CloudflareCaptchaModel $model;

    private string $model_name = 'Cloudflare CAPTCHA';
    private string $model_key = 'cloudflare_captcha';
    private string $post_key = 'cf-turnstile-response';
    private bool $use = false;
    private string $client_key = '';
    private string $server_key = '';

    public static function make(): CloudflareCaptchaModel
    {
        if (!isset(self::$model)) {
            self::$model = new CloudflareCaptchaModel();
        }
        return self::$model;
    }

    public function getService(): CloudflareCaptchaService
    {
        return new CloudflareCaptchaService();
    }

    public function getModelName(): string
    {
        return $this->model_name;
    }

    public function getModelKey(): string
    {
        return $this->model_key;
    }

    public function getModelPostKey(): string
    {
        return $this->post_key;
    }

    public function isUse(): bool
    {
        return $this->use;
    }

    public function setUse(bool $use): CloudflareCaptchaModel
    {
        $this->use = $use;
        return $this;
    }

    public function getClientKey(): string
    {
        return $this->client_key;
    }

    public function setClientKey(string $client_key): CloudflareCaptchaModel
    {
        $this->client_key = $client_key;
        return $this;
    }

    public function getServerKey(): string
    {
        return $this->server_key;
    }

    public function setServerKey(string $server_key): CloudflareCaptchaModel
    {
        $this->server_key = $server_key;
        return $this;
    }
}
