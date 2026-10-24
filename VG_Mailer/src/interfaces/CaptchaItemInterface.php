<?php

namespace VG_Mailer\src\interfaces;

interface CaptchaItemInterface
{
    public function isUse(): bool;

    public function getService(): CaptchaItemServiceInterface;

    public function getModelName(): string;

    public function getModelKey(): string;

    public function getClientKey(): string;

    public function getModelPostKey(): string;
}
