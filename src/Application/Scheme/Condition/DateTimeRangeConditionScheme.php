<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;
use DateTimeImmutable;

class DateTimeRangeConditionScheme implements RoutingStepSchemeInterface
{
    public DateTimeImmutable $from;
    public DateTimeImmutable $to;
}
