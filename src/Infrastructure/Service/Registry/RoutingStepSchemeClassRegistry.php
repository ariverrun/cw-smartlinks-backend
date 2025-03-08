<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Application\Exception\UnknownRoutingStepSchemeException;
use InvalidArgumentException;

class RoutingStepSchemeClassRegistry implements RoutingStepSchemeClassRegistryInterface
{
    /**
     * @param array<string,string> $routeStepSchemeClassesByAliases
     */
    public function __construct(
        private readonly array $routeStepSchemeClassesByAliases,
    ) {
        foreach ($routeStepSchemeClassesByAliases as $class) {
            if (!\is_subclass_of($class, RoutingStepSchemeInterface::class)) {
                throw new InvalidArgumentException();
            }
        }
    }

    public function getRoutingStepSchemeClassByAlias(string $alias): string
    {
        if (!isset($this->routeStepSchemeClassesByAliases[$alias])) {
            throw new UnknownRoutingStepSchemeException();
        }

        return $this->routeStepSchemeClassesByAliases[$alias];
    }
}
