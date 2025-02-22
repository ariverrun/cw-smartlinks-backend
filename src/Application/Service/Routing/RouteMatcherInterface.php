<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

interface RouteMatcherInterface
{
    public function findMatchingRouteIdForUrl(string $url): ?int;
}
