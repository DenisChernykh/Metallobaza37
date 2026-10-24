<?php

namespace VG_Mailer\src\interfaces;

use VG_Mailer\src\Response;

interface SenderWorkerInterface
{
    public function canSend(): bool;

    public function send(array $data): Response;

    public function buildMessage(array $data): string;

    public function getLabel(): string;
}