<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\HttpRequestDto;
use App\Application\Service\Routing\RouteMatcherInterface;
use App\Application\Exception\MachingRouteIsNotFoundException;

class GetRedirectUrlForHttpRequestUseCase implements GetRedirectUrlForHttpRequestUseCaseInterface
{
    public function __construct(
        private readonly RouteMatcherInterface $routeMatcher,
    ) {
    }

    public function __invoke(HttpRequestDto $dto): string
    {
        $inputUrlId = $this->routeMatcher->findMatchingRouteIdForUrl($dto->requestPath);

        if (null === $inputUrlId) {
            throw new MachingRouteIsNotFoundException();
        }

        return '';
    }
}
