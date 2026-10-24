<?php

namespace VG_Mailer\src\models;

use VG_Mailer\src\interfaces\SenderWorkerModelInterface;

class TelegramModel implements SenderWorkerModelInterface
{
    private static TelegramModel $model;
    protected bool $use = false;
    protected ?string $token = null;
    protected ?int $chatId = null;
    protected ?string $parseMode = 'markdown';

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new TelegramModel();
        }
        return self::$model;
    }

    /**
     * @return string
     */
    public function getParseMode(): string
    {
        return $this->parseMode;
    }

    /**
     * @param string $parseMode
     * @return TelegramModel
     */
    public function setParseMode(string $parseMode): TelegramModel
    {
        $this->parseMode = $parseMode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUse(): bool
    {
        return $this->use;
    }

    /**
     * @param bool $use
     * @return TelegramModel
     */
    public function setUse(bool $use): TelegramModel
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return TelegramModel
     */
    public function setToken(?string $token): TelegramModel
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    /**
     * @param int|null $chatId
     * @return TelegramModel
     */
    public function setChatId(?int $chatId): TelegramModel
    {
        $this->chatId = $chatId;
        return $this;
    }
}