<?php

declare(strict_types=1);

namespace App\Application\RouteStepScheme\Redirect;

use App\Application\RouteStepScheme\RouteStepSchemeInterface;

class RedirectScheme implements RouteStepSchemeInterface
{
    public string $url;
}