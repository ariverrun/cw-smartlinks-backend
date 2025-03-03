<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;

class CountryConditionScheme implements RoutingStepSchemeInterface
{
    /**
     * @var string[]
     */
    public array $countries;
}
