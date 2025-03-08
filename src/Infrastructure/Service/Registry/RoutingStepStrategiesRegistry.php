<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\Exception\RoutingStepStrategyIsNotFoundException;
use App\Application\Service\Registry\RoutingStepStrategiesRegistryInterface;
use App\Application\Service\Strategy\RoutingStepStrategyInterface;

class RoutingStepStrategiesRegistry implements RoutingStepStrategiesRegistryInterface
{
    /**
     * @var array<string,RoutingStepStrategyInterface>
     */
    private array $routingStepStrategiesByAlias = [];

    public function getStrategyByAlias(string $alias): RoutingStepStrategyInterface
    {
        if (!isset($this->routingStepStrategiesByAlias[$alias])) {
            throw new RoutingStepStrategyIsNotFoundException();
        }

        return $this->routingStepStrategiesByAlias[$alias];
    }

    public function addStrategy(string $indexKey, RoutingStepStrategyInterface $routingStepStrategy): void
    {
        $this->routingStepStrategiesByAlias[$indexKey] = $routingStepStrategy;
    }
}
