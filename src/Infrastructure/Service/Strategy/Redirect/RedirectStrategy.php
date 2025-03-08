<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Redirect;

use App\Application\Attribute\SupportedRoutingStepScheme;
use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Scheme\Redirect\RedirectScheme;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Handler\RoutingStepHandlerResult;
use App\Infrastructure\Service\Strategy\RoutingStepStrategy;

#[SupportedRoutingStepScheme(class: RedirectScheme::class)]
final class RedirectStrategy extends RoutingStepStrategy
{
    /**
     * @param RedirectScheme $routingStepScheme
     */
    protected function doRouteStepTypeSpecificHandling(
        RoutingStepInterface $routingStep,
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface {
        $redirectUrl = preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($context): string {
            $requestPathParam = $matches[1];

            return (string)($context->hasParameter($requestPathParam) ?
                        $context->getParameter($requestPathParam) :
                        $matches[0]);
        }, $routingStepScheme->url);

        return new RoutingStepHandlerResult(
            redirectUrl: $redirectUrl,
        );
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof RedirectScheme;
    }
}
