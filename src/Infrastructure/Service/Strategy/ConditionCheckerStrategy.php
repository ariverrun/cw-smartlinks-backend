<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStep;
use App\Infrastructure\Service\Handler\RoutingStepHandlerResult;

abstract class ConditionCheckerStrategy extends RoutingStepStrategy
{
    protected function doRouteStepTypeSpecificHandling(
        RoutingStep $routingStep,
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface {

        $isConditionMet = $this->meetsCondtion(
            $routingStepScheme,
            $httpRequestDto,
            $context,
        );

        return new RoutingStepHandlerResult(
            $isConditionMet ? $routingStep->getOnPassStep() : $routingStep->getOnDeclineStep()
        );
    }

    abstract protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool;
}
