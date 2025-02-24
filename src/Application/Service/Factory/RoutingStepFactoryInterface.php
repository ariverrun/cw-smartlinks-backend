<?php

declare(strict_types=1);

namespace App\Application\Service\Factory;

use App\Application\Dto\RoutingStepNestedDto;
use App\Domain\Entity\Route;
use App\Domain\Entity\RoutingStepInterface;

interface RoutingStepFactoryInterface
{
    public function createRoutingStep(RoutingStepNestedDto $dto, Route $route): RoutingStepInterface;
}
