<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Exception\UnknowRoutingStepTypeException;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Domain\Entity\RoutingStep;
use InvalidArgumentException;

class RoutingStepClassRegistry implements RoutingStepClassRegistryInterface
{
    private readonly array $aliasesByClass;
    /**
     * @var array<string,string>
     */
    public function __construct(
        private readonly array $classesByAlias,
    ) {
        foreach ($classesByAlias as $class) {
            if (!\is_subclass_of($class, RoutingStep::class)) {
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
