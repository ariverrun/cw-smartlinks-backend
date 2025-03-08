<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Condition;

use App\Application\Attribute\SupportedRoutingStepScheme;
use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\LocaleConditionScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;

#[SupportedRoutingStepScheme(class: LocaleConditionScheme::class)]
final class LocaleCheckerStrategy extends ConditionCheckerStrategy
{
    /**
     * @param LocaleConditionScheme $routingStepScheme
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
        return $routingStepScheme instanceof LocaleConditionScheme;
    }
}
