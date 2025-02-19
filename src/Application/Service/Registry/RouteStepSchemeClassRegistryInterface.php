<?php

declare(strict_types=1);

namespace App\Application\Service\Registry;

use App\Application\Exception\UnknownRouteStepSchemeException;

interface RouteStepSchemeClassRegistryInterface
{
    /**
     * @throws UnknownRouteStepSchemeException
     */
    public function getRouteStepSchemeClassByAlias(string $alias): string;
}
