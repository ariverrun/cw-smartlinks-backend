<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RoutingMapProviderInterface;
use App\Application\Service\Routing\RoutingMapConstantsHolder;
use App\Domain\Repository\RouteRepositoryInterface;

class RoutingMapProvider implements RoutingMapProviderInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getRoutingMap(): array
    {
        $routingMap = [];

        $routes = $this->routeRepository->findAllActiveDescByPriority();

        foreach ($routes as $route) {
            $urlPatternParts = explode('/', $route->getUrlPattern());
            unset($urlPatternParts[0]);

            if (!isset($routingMap[$route->getPriority()])) {
                $routingMap[$route->getPriority()] = [];
            }

            $tail = &$routingMap[$route->getPriority()];
            $urlPatternPartsCount = count($urlPatternParts);

            foreach ($urlPatternParts as $i => $part) {
                if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                    $part = RoutingMapConstantsHolder::REQUIRED_PARAM;
                } elseif ('*' === $part) {
                    $part = RoutingMapConstantsHolder::MATCHES_ALL;
                }

                if (!isset($tail[$part])) {
                    $tail[$part] = [];
                }

                if ($i === $urlPatternPartsCount) {
                    $tail[$part][RoutingMapConstantsHolder::TERMINAL_KEY] = $route->getId();
                } else {
                    $tail = &$tail[$part];
                }
            }
        }

        return $routingMap;
    }
}
