<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Exception\UnknowRouteStepTypeException;
use App\Application\Service\Registry\RouteStepClassRegistryInterface;
use App\Domain\Entity\RouteStep;
use InvalidArgumentException;

class RouteStepClassRegistry implements RouteStepClassRegistryInterface
{
    private readonly array $aliasesByClass;
    /**
     * @var array<string,string>
     */
    public function __construct(
        private readonly array $classesByAlias,
    ) {
        foreach ($classesByAlias as $class) {
            if (!\is_subclass_of($class, RouteStep::class)) {
                throw new InvalidArgumentException(\sprintf('%s is not subclass of %s', $class, RouteStep::class));
            }
        }
        $this->aliasesByClass = \array_flip($classesByAlias);
    }

    public function getRouteStepClassByAlias(string $alias): string
    {
        if (!isset($this->classesByAlias[$alias])) {
            throw new UnknowRouteStepTypeException(\sprintf('Route step type with %s alias is not found', $alias));
        }

        return $this->classesByAlias[$alias];
    }

    public function getAliasForRouteStepClass(string $class): string
    {
        if (!isset($this->aliasesByClass[$class])) {
            throw new UnknowRouteStepTypeException(\sprintf('Alias for %s route step type is not found', $class));
        }

        return $this->aliasesByClass[$class];
    }
}
