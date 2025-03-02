<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\LocaleCondtionScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;

final class LocaleCheckerStrategy extends ConditionCheckerStrategy
{
    /**
     * @param LocaleCondtionScheme $routingStepScheme
     */
    protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool {
        return  in_array($httpRequestDto->locale, $routingStepScheme->locales);
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof LocaleCondtionScheme;
    }
}
