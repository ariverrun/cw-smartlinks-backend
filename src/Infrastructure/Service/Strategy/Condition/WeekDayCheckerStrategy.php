<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\WeekDayConditionScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;

final class WeekDayCheckerStrategy extends ConditionCheckerStrategy
{
    /**
     * @param WeekDayConditionScheme $routingStepScheme
     */
    protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool {
        return  in_array($httpRequestDto->requestTime->format('N'), $routingStepScheme->weekDays);
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof WeekDayConditionScheme;
    }
}
