<?php

namespace VG_Mailer\src;

class Route
{

    private array $data;

    private string $action;

    private array $handler;

    private ?array $middlewares;

    public function __construct(array $data, string $action, array $handler)
    {
        $this->data = $data;
        $this->action = $action;
        $this->handler = $handler;
        $this->middlewares = null;
    }

    public function middleware(...$middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getMiddlewares(): ?array
    {
        return $this->middlewares;
    }

}