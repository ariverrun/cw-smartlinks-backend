<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RoutingMapProviderInterface;
use App\Domain\Repository\RouteRepositoryInterface;

class RoutingMapProvider implements RoutingMapProviderInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function getRoutingMap(): array
    {
        /**
         * @var array<int, RoutingMapNode>
         */
        $prioritizedRoutingMap = [];

        $routes = $this->routeRepository->findAllActiveDescByPriority();

        foreach ($routes as $route) {
            $urlPatternParts = explode('/', $route->getUrlPattern());
            unset($urlPatternParts[0]);

            $priority = $route->getPriority();

            if (!isset($prioritizedRoutingMap[$priority])) {
                $prioritizedRoutingMap[$priority] = new RoutingMapNode();
            }

            $tailNode = $prioritizedRoutingMap[$priority];

            $urlPatternPartsCount = count($urlPatternParts);

            foreach ($urlPatternParts as $i => $part) {
                $node = new RoutingMapNode();

                if ($i === $urlPatternPartsCount) {
                    $node->setTerminalRouteId($route->getId());
                }

                if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                    $tailNode->addRequiredParamNode($node);
                } elseif ('*' === $part) {
                    $tailNode->addMatchingAllNode($node);
                } else {
                    $tailNode->addKeyNode($part, $node);
                }

                $tailNode = $node;
            }

        }

        return $prioritizedRoutingMap;
    }
}
