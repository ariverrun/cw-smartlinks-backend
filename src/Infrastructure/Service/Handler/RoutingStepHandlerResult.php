<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Handler;

use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Domain\Entity\RoutingStepInterface;

class RoutingStepHandlerResult implements RoutingStepHandlerResultInterface
{
    public function __construct(
        private ?RoutingStepInterface $nextStep = null,
        private ?string $redirectUrl = null,
    ) {
    }

    public function getNextStep(): ?RoutingStepInterface
    {
        return $this->nextStep;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
}
