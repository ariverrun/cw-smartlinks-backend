<?php

declare(strict_types=1);

namespace App\Application\Service\Registry;

use App\Application\Exception\UnknowRouteStepTypeException;

interface RouteStepClassRegistryInterface
{
    /**
     * @throws UnknowRouteStepTypeException
     */
    public function getRouteStepClassByAlias(string $alias): string;

    /**
     * @throws UnknowRouteStepTypeException
     */
    public function getAliasForRouteStepClass(string $class): string;
}
