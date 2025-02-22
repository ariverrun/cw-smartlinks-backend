<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Exception\RoutingStepStrategyIsNotFoundException;
use App\Application\Service\Registry\RoutingStepStrategiesRegistryInterface;
use App\Application\Service\Strategy\RoutingStepStrategyInterface;
use InvalidArgumentException;

class RoutingStepStrategiesRegistry implements RoutingStepStrategiesRegistryInterface
{
    /**
     * @param array<string,RoutingStepStrategyInterface> $strategiesByAlias
     */
    public function __construct(
        private readonly array $strategiesByAlias,
    ) {
        foreach ($strategiesByAlias as $strategy) {
            if (!$strategy instanceof RoutingStepStrategyInterface) {
                throw new InvalidArgumentException();
            }
        }
    }

    public function getStrategyByAlias(string $alias): RoutingStepStrategyInterface
    {
        if (!isset($this->strategiesByAlias[$alias])) {
            throw new RoutingStepStrategyIsNotFoundException();
        }

        return $this->strategiesByAlias[$alias];
    }
}
