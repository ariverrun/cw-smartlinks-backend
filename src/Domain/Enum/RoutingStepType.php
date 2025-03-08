<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum RoutingStepType: string
{
    case CONDITION = 'condition';
    case REDIRECT = 'redirect';
}
