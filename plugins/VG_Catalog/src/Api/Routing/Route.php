<?php

namespace VG_Catalog\Api\Routing;

class Route
{

    private array $handler;
    private ?array $middlewares;

    public function __construct(array $handler)
    {
        $this->handler = $handler;
        $this->middlewares = null;
    }

    public function middleware(...$middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
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