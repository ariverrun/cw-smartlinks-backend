<?php

declare(strict_types=1);

namespace App\Application\Service\Handler;

use App\Domain\Entity\RoutingStep;

interface RoutingStepHandlerResultInterface
{
    public function getNextStep(): ?RoutingStep;
    public function getRedirectUrl(): ?string;
}
