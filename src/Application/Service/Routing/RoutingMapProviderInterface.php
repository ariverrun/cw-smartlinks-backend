<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

interface RoutingMapProviderInterface
{
    /**
     * @return array<int, RoutingMapNodeInterface>
     */
    public function getRoutingMap(): array;
}
