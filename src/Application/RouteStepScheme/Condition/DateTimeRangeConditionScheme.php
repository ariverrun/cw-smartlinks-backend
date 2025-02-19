<?php

declare(strict_types=1);

namespace App\Application\RouteStepScheme\Condition;

use App\Application\RouteStepScheme\RouteStepSchemeInterface;
use \DateTimeImmutable;

class DateTimeRangeConditionScheme implements RouteStepSchemeInterface
{
    public DateTimeImmutable $from;
    public DateTimeImmutable $to;
}