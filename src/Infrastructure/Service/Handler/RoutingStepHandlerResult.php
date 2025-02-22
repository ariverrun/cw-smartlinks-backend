<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Handler;

use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Domain\Entity\RoutingStep;

class RoutingStepHandlerResult implements RoutingStepHandlerResultInterface
{
    public function __construct(
        private ?RoutingStep $nextStep = null,
        private ?string $redirectUrl = null,
    ) {
    }

    public function getNextStep(): ?RoutingStep
    {
        return $this->nextStep;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
}
