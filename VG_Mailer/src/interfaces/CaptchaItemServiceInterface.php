<?php

namespace VG_Mailer\src\interfaces;

interface CaptchaItemServiceInterface
{
    /**
     * Проверяем настройки
     * @param  CaptchaItemInterface  $model
     * @return mixed
     */
    public function checkSettings(CaptchaItemInterface $model): bool;

    /**
     * Получаем src на скрипт капчи
     * @return string
     */
    public function getScriptSrc(CaptchaItemInterface $model): ?string;

    /**
     * Проверяем капчу
     * @param  array  $data
     * @return bool
     */
    public function checkCaptcha(array $data): bool;
}
