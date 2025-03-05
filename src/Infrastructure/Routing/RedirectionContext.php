<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RedirectionContextInterface;

class RedirectionContext implements RedirectionContextInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private readonly array $params,
    ) {
    }

    public function hasParameter(string $paramName): bool
    {
        return isset($this->params[$paramName]);
    }

    public function getParameter(string $paramName): mixed
    {
        return $this->params[$paramName] ?? null;
    }
}
