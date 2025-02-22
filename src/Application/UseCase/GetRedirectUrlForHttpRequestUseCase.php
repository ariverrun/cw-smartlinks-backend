<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\MachingRouteIsNotFoundException;
use App\Application\Service\Routing\RedirectUrlResolverInterface;
use App\Application\Service\Routing\RouteMatcherInterface;

class GetRedirectUrlForHttpRequestUseCase implements GetRedirectUrlForHttpRequestUseCaseInterface
{
    public function __construct(
        private readonly RouteMatcherInterface $routeMatcher,
        private readonly RedirectUrlResolverInterface $redirectUrlResolver,
    ) {
    }

    public function __invoke(HttpRequestDto $dto): string
    {
        $routeId = $this->routeMatcher->findMatchingRouteIdForUrl($dto->requestPath);

        if (null === $routeId) {
            throw new MachingRouteIsNotFoundException();
        }

        $redirectUrl = $this->redirectUrlResolver->resolveRedirectUrl($routeId, $dto);

        return $redirectUrl;
    }
}
