<?php

declare(strict_types=1);

namespace App\Application\Service\Registry;

use App\Application\Exception\RoutingStepStrategyIsNotFoundException;
use App\Application\Service\Strategy\RoutingStepStrategyInterface;

interface RoutingStepStrategiesRegistryInterface
{
    /**
     * @throws RoutingStepStrategyIsNotFoundException
     */
    public function getStrategyByAlias(string $alias): RoutingStepStrategyInterface;
}
