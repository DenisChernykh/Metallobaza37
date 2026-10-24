<?php

namespace VG_Mailer\src\models;

class EmailSMTPModel
{
    private static EmailSMTPModel $model;

    protected ?string $server = null;
    protected ?string $login = null;
    protected ?string $password = null;
    protected ?int $port = null;
    protected ?string $secure = null;

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new EmailSMTPModel();
        }
        return self::$model;
    }

    /**
     * @return string|null
     */
    public function getSecure(): ?string
    {
        return $this->secure;
    }

    /**
     * @param  string|null  $secure
     * @return EmailSMTPModel
     */
    public function setSecure(?string $secure): EmailSMTPModel
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getServer(): ?string
    {
        return $this->server;
    }

    /**
     * @param  string|null  $server
     * @return EmailSMTPModel
     */
    public function setServer(?string $server): EmailSMTPModel
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @param  string|null  $login
     * @return EmailSMTPModel
     */
    public function setLogin(?string $login): EmailSMTPModel
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param  string|null  $password
     * @return EmailSMTPModel
     */
    public function setPassword(?string $password): EmailSMTPModel
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param  int|null  $port
     * @return EmailSMTPModel
     */
    public function setPort(?int $port): EmailSMTPModel
    {
        $this->port = $port;
        return $this;
    }
}