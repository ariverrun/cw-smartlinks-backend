<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

interface RoutingMapNodeInterface
{
    /**
     * @return RoutingMapNodeInterface[]
     */
    public function getNodesByKey(string $routePartKey): array;

    /**
     * @return RoutingMapNodeInterface[]
     */
    public function getNodesByRequiredParam(): array;

    /**
     * @return RoutingMapNodeInterface[]
     */
    public function getNodesMatchingAll(): array;

    public function getTerminalRouteId(): ?int;
}
