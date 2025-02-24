<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\DateTimeRangeConditionScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;

final class DateTimeRangeCheckerStrategy extends ConditionCheckerStrategy
{
    /**
     * @param DateTimeRangeConditionScheme $routingStepScheme
     */
    protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool {
        return $httpRequestDto->requestTime >= $routingStepScheme->from && $httpRequestDto->requestTime < $routingStepScheme->to;
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof DateTimeRangeConditionScheme;
    }
}
