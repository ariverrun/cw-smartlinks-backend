<?php

declare(strict_types=1);

namespace App\Application\Service\Handler;

use App\Domain\Entity\RoutingStepInterface;

interface RoutingStepHandlerResultInterface
{
    public function getNextStep(): ?RoutingStepInterface;
    public function getRedirectUrl(): ?string;
}
