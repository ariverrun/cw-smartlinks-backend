<?php

declare(strict_types=1);

namespace App\Application\Service\Strategy;

use App\Application\Dto\HttpRequestDto;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStep;

interface RoutingStepStrategyInterface
{
    public function doHandleRoutingStep(
        RoutingStep $routingStep,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface;
}
