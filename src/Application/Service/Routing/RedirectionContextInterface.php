<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

interface RedirectionContextInterface
{
    public function hasParameter(string $paramName): bool;
    public function getParameter(string $paramName): mixed;
}
