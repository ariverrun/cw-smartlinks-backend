<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

/**
 * @phpstan-type RoutePathPart array<string,RoutePathPart>|int
 */
interface RoutingMapProviderInterface
{
    /**
     * @return array<int,array<string,RoutePathPart>>
     */
    public function getRoutingMap(): array;
}
