<?php

declare(strict_types=1);

namespace App\Application\Service\Handler;

use App\Application\Dto\HttpRequestDto;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStep;

interface RoutingStepHandlerInterface
{
    public function handleRoutingStep(
        RoutingStep $routingStep,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface;
}
