<?php

declare(strict_types=1);

namespace App\Application\Scheme\Redirect;

use App\Application\Scheme\RoutingStepSchemeInterface;

class RedirectScheme implements RoutingStepSchemeInterface
{
    public string $url;
}
