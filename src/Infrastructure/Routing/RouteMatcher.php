<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RoutingMapConstantsHolder;
use App\Application\Service\Routing\RouteMatcherInterface;
use App\Application\Service\Routing\RoutingMapProviderInterface;

/**
 * @phpstan-type RoutePathPart array<string,RoutePathPart>|int
 */
class RouteMatcher implements RouteMatcherInterface
{
    public function __construct(
        private readonly RoutingMapProviderInterface $routingMapProvider,
    ) {
    }

    public function findMatchingRouteIdForUrl(string $url): ?int
    {
        $routingMap = $this->routingMapProvider->getRoutingMap();

        $urlParts = explode('/', $url);
        unset($urlParts[0]);
        $urlParts = array_values($urlParts);

        foreach ($routingMap as $prioritizedRoutes) {
            foreach ($prioritizedRoutes as $routePart => $childRouteParts) {
                $routeId = $this->findRecursively($urlParts, $routePart, $childRouteParts);

                if (null !== $routeId) {
                    return $routeId;
                }
            }
        }

        return null;
    }

    /**
     * @param string[] $urlParts
     * @param RoutePathPart $routePathPart
     */
    private function findRecursively(array $urlParts, string $routePart, array $routePathPart): ?int
    {
        if ($routePart === RoutingMapConstantsHolder::MATCHES_ALL) {
            if (isset($routePathPart[RoutingMapConstantsHolder::TERMINAL_KEY])) {
                return $routePathPart[RoutingMapConstantsHolder::TERMINAL_KEY];
            }
        }

        if (
            $routePart === $urlParts[0]
            || (str_starts_with($routePart, '{') && str_ends_with($routePart, '}'))
        ) {
            unset($urlParts[0]);
            $urlParts = array_values($urlParts);

            if (empty($urlParts) && count($routePathPart) === 1 && isset($routePathPart[RoutingMapConstantsHolder::TERMINAL_KEY])) {
                return $routePathPart[RoutingMapConstantsHolder::TERMINAL_KEY];
            } elseif (!empty($urlParts) && count($routePathPart) > 1) {
                foreach ($routePathPart as $routePart => $childRoutePart) {
                    if (is_int($childRoutePart)) {
                        if (empty($urlParts)) {
                            return $childRoutePart;
                        }

                        continue;

                    }

                    $routeId = $this->findRecursively($urlParts, $routePart, $childRoutePart);

                    if (null !== $routeId) {
                        return $routeId;
                    }
                }
            } elseif (!empty($urlParts) && count($routePathPart) === 1 && isset($routePathPart[RoutingMapConstantsHolder::MATCHES_ALL])) {
                if (isset($routePathPart[RoutingMapConstantsHolder::MATCHES_ALL][RoutingMapConstantsHolder::TERMINAL_KEY])) {
                    return $routePathPart[RoutingMapConstantsHolder::MATCHES_ALL][RoutingMapConstantsHolder::TERMINAL_KEY];
                }
            } else {
                return null;
            }
        }

        return null;
    }
}
