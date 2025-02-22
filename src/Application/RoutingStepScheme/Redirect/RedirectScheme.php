<?php

declare(strict_types=1);

namespace App\Application\RoutingStepScheme\Redirect;

use App\Application\RoutingStepScheme\RoutingStepSchemeInterface;

class RedirectScheme implements RoutingStepSchemeInterface
{
    public string $url;
}
