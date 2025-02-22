<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\RedirectUrlIsNotResolvedException;
use App\Application\Service\Handler\RoutingStepHandlerInterface;
use App\Application\Service\Routing\RedirectUrlResolverInterface;
use App\Domain\Repository\RouteRepositoryInterface;
use RuntimeException;

class RedirectUrlResolver implements RedirectUrlResolverInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly RoutingStepHandlerInterface $routingStepHandler,
    ) {
    }

    public function resolveRedirectUrl(int $routeId, HttpRequestDto $httpRequestDto): string
    {
        $route = $this->routeRepository->findOneById($routeId);

        if (null === $route) {
            throw new RuntimeException();
        }

        $context = new RedirectionContext();

        $routingStep = $route->getInitialStep();

        do {
            $result = $this->routingStepHandler->handleRoutingStep(
                $routingStep,
                $httpRequestDto,
                $context
            );

            $routingStep = $result->getNextStep();

        } while (null !== $routingStep);

        $redirectUrl = $result->getRedirectUrl();

        if (null === $redirectUrl) {
            throw new RedirectUrlIsNotResolvedException();
        }

        return $redirectUrl;
    }
}
