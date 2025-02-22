<?php

declare(strict_types=1);

namespace App\Application\Service\Registry;

use App\Application\Exception\UnknowRoutingStepTypeException;

interface RoutingStepClassRegistryInterface
{
    /**
     * @throws UnknowRoutingStepTypeException
     */
    public function getRoutingStepClassByAlias(string $alias): string;

    /**
     * @throws UnknowRoutingStepTypeException
     */
    public function getAliasForRoutingStepClass(string $class): string;
}
