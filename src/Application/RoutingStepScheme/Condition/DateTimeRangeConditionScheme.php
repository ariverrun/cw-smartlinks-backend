<?php

declare(strict_types=1);

namespace App\Application\RoutingStepScheme\Condition;

use App\Application\RoutingStepScheme\RoutingStepSchemeInterface;
use DateTimeImmutable;

class DateTimeRangeConditionScheme implements RoutingStepSchemeInterface
{
    public DateTimeImmutable $from;
    public DateTimeImmutable $to;
}
