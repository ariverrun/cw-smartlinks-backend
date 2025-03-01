<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RouteMatcherInterface;
use App\Application\Service\Routing\RoutingMapNodeInterface;
use App\Application\Service\Routing\RoutingMapProviderInterface;

class RouteMatcher implements RouteMatcherInterface
{
    public function __construct(
        private readonly RoutingMapProviderInterface $routingMapProvider,
    ) {
    }

    public function findMatchingRouteIdForUrl(string $url): ?int
    {
        $prioritizedRoutingMap = $this->routingMapProvider->getRoutingMap();

        $urlParts = explode('/', $url);
        unset($urlParts[0]);
        $urlParts = array_values($urlParts);

        foreach ($prioritizedRoutingMap as $mapNode) {
            $routeId = $this->findRouteIdRecursivelyInMapNode($urlParts, $mapNode);

            if (null !== $routeId) {
                return $routeId;
            }
        }

        return null;
    }

    /**
     * @param string[] $urlParts
     */
    private function findRouteIdRecursivelyInMapNode(array $urlParts, RoutingMapNodeInterface $node): ?int
    {
        if (empty($urlParts)) {
            return $node->getTerminalRouteId();
        }

        foreach ($node->getNodesMatchingAll() as $matchingAllNode) {
            if (null !== $matchingAllNode->getTerminalRouteId()) {
                return $matchingAllNode->getTerminalRouteId();
            }
        }

        $currentUrlPart = array_shift($urlParts);

        foreach ($node->getNodesByKey($currentUrlPart) as $nodeWithSameKey) {
            $routeId = $this->findRouteIdRecursivelyInMapNode($urlParts, $nodeWithSameKey);

            if (null !== $routeId) {
                return $routeId;
            }
        }

        foreach ($node->getNodesByRequiredParam() as $requiredParamNode) {
            $routeId = $this->findRouteIdRecursivelyInMapNode($urlParts, $requiredParamNode);

            if (null !== $routeId) {
                return $routeId;
            }
        }

        return $node->getTerminalRouteId();
    }
}
