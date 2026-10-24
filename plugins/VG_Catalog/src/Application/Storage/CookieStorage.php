<?php

namespace VG_Catalog\Application\Storage;

class CookieStorage
{
    protected string $name;
    protected int $lifetime;
    protected string $path;
    protected string $domain;
    protected bool $secure;
    protected bool $httpOnly;

    public function __construct(
        string $name,
        int $lifetime = 2592000, // 30 дней
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true
    ) {
        $this->name = $name;
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    public function get(): array
    {
        $cookieValue = $_COOKIE[$this->name] ?? 'COOKIE_NOT_FOUND';

        if (empty($cookieValue) || $cookieValue === 'COOKIE_NOT_FOUND') {
            return [];
        }

        $unescapedValue = stripslashes($cookieValue);
        $data = json_decode($unescapedValue, true);

        return is_array($data) ? $data : [];
    }

    public function set(array $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        setcookie(
            $this->name,
            $json,
            [
                'expires' => time() + $this->lifetime,
                'path' => $this->path,
                'domain' => $this->domain,
                'secure' => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => 'Lax'
            ]
        );

        $_COOKIE[$this->name] = $json;
    }

    public function clear(): void
    {
        setcookie($this->name, '', time() - 3600, $this->path);
        unset($_COOKIE[$this->name]);
    }
}
