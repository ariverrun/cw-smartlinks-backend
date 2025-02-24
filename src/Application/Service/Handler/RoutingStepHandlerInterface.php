<?php

declare(strict_types=1);

namespace App\Application\Service\Handler;

use App\Application\Dto\HttpRequestDto;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Domain\Entity\RoutingStepInterface;

interface RoutingStepHandlerInterface
{
    public function handleRoutingStep(
        RoutingStepInterface $routingStep,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface;
}
