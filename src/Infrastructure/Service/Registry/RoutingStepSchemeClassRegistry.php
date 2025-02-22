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
     * @param array<string,string> $classesByAlias
     */
    public function __construct(
        private readonly array $classesByAlias,
    ) {
        foreach ($classesByAlias as $class) {
            if (!\is_subclass_of($class, RoutingStepSchemeInterface::class)) {
                throw new InvalidArgumentException();
            }
        }
    }

    public function getRoutingStepSchemeClassByAlias(string $alias): string
    {
        if (!isset($this->classesByAlias[$alias])) {
            throw new UnknownRoutingStepSchemeException();
        }

        return $this->classesByAlias[$alias];
    }
}
