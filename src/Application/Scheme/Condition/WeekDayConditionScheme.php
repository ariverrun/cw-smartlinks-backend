<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;

class WeekDayConditionScheme implements RoutingStepSchemeInterface
{
    /**
     * @var int[]
     */
    public array $weekDays;
}
