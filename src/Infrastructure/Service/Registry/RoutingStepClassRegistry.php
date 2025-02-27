<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Exception\UnknowRoutingStepTypeException;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Domain\Entity\RoutingStepInterface;
use InvalidArgumentException;

class RoutingStepClassRegistry implements RoutingStepClassRegistryInterface
{
    /**
     * @var array<string,string>
     */
    private readonly array $aliasesByClass;

    /**
     * @param array<string,string> $classesByAlias
     */
    public function __construct(
        private readonly array $classesByAlias,
    ) {
        foreach ($classesByAlias as $class) {
            if (!\is_subclass_of($class, RoutingStepInterface::class)) {
                throw new InvalidArgumentException();
            }
        }
        $this->aliasesByClass = \array_flip($classesByAlias);
    }

    public function getRoutingStepClassByAlias(string $alias): string
    {
        if (!isset($this->classesByAlias[$alias])) {
            throw new UnknowRoutingStepTypeException();
        }

        return $this->classesByAlias[$alias];
    }

    public function getAliasForRoutingStepClass(string $class): string
    {
        if (!isset($this->aliasesByClass[$class])) {
            throw new UnknowRoutingStepTypeException();
        }

        return $this->aliasesByClass[$class];
    }
}
