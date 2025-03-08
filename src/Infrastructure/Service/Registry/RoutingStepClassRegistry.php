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
    private readonly array $aliasesByRoutingStepClass;

    /**
     * @param array<string,string> $routingStepClassesByAlias
     */
    public function __construct(
        private readonly array $routingStepClassesByAlias,
    ) {
        foreach ($routingStepClassesByAlias as $class) {
            if (!\is_subclass_of($class, RoutingStepInterface::class)) {
                throw new InvalidArgumentException();
            }
        }
        $this->aliasesByRoutingStepClass = \array_flip($routingStepClassesByAlias);
    }

    public function getRoutingStepClassByAlias(string $alias): string
    {
        if (!isset($this->routingStepClassesByAlias[$alias])) {
            throw new UnknowRoutingStepTypeException();
        }

        return $this->routingStepClassesByAlias[$alias];
    }

    public function getAliasForRoutingStepClass(string $class): string
    {
        if (!isset($this->aliasesByRoutingStepClass[$class])) {
            throw new UnknowRoutingStepTypeException();
        }

        return $this->aliasesByRoutingStepClass[$class];
    }
}
