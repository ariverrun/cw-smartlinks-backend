<?php

declare(strict_types=1);

namespace App\Application\Service\Registry;

use App\Application\Exception\UnknownRoutingStepSchemeException;

interface RoutingStepSchemeClassRegistryInterface
{
    /**
     * @throws UnknownRoutingStepSchemeException
     */
    public function getRoutingStepSchemeClassByAlias(string $alias): string;
}
