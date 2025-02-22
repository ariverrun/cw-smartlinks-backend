<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

final readonly class RoutingMapConstantsHolder
{
    public const REQUIRED_PARAM = '{r}';
    public const MATCHES_ALL = '*';
    public const TERMINAL_KEY = '';

    private function __construct()
    {
    }
}
