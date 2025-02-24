<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

/**
 * @phpstan-type RoutePathPart array<string,RoutePathPart|int>
 *
 * @phpstan-ignore typeAlias.circular
 */
interface RoutingMapProviderInterface
{
    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public function getRoutingMap(): array;
}
