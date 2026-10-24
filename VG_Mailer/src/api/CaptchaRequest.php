<?php

namespace VG_Mailer\src\api;

use VG_Mailer\src\services\captcha\CaptchaService;

class CaptchaRequest
{
    private CaptchaService $captchaService;

    public function __construct()
    {
        $this->captchaService = new CaptchaService();
    }

    public function getData(): void
    {
        $this->captchaService->getData()->json();
    }
}