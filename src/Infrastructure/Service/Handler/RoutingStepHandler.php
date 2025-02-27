<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Handler;

use App\Application\Dto\HttpRequestDto;
use App\Application\Service\Handler\RoutingStepHandlerInterface;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepStrategiesRegistryInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStepInterface;

class RoutingStepHandler implements RoutingStepHandlerInterface
{
    public function __construct(
        private readonly RoutingStepClassRegistryInterface $routingStepClassRegistry,
        private readonly RoutingStepStrategiesRegistryInterface $routingStepStrategiesRegistry,
    ) {
    }

    public function handleRoutingStep(
        RoutingStepInterface $routingStep,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface {

        $routingStepClassAlias = $this->routingStepClassRegistry->getAliasForRoutingStepClass($routingStep::class);

        $strategy = $this->routingStepStrategiesRegistry->getStrategyByAlias(
            $routingStepClassAlias . '.' . $routingStep->getSchemeType()
        );

        return $strategy->doHandleRoutingStep($routingStep, $httpRequestDto, $context);

    }
}
