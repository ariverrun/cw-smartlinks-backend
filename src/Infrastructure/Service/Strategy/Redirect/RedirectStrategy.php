<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Redirect;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Scheme\Redirect\RedirectScheme;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStep;
use App\Infrastructure\Service\Handler\RoutingStepHandlerResult;
use App\Infrastructure\Service\Strategy\RoutingStepStrategy;

final class RedirectStrategy extends RoutingStepStrategy
{
    /**
     * @param RedirectScheme $routingStepScheme
     */
    protected function doRouteStepTypeSpecificHandling(
        RoutingStep $routingStep,
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface {
        return new RoutingStepHandlerResult(
            redirectUrl: $routingStepScheme->url,
        );
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof RedirectScheme;
    }
}
