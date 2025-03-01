<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;

class LocaleConditionScheme implements RoutingStepSchemeInterface
{
    /**
     * @var string[]
     */
    public array $locales;
}
