<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Domain\Enum\RoutingStepType;
use Symfony\Component\Validator\Constraints as Assert;

#[RoutingStepScheme(type: RoutingStepType::CONDITION->value, alias: 'week_day')]
final class WeekDayConditionScheme implements RoutingStepSchemeInterface
{
    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All(
        constraints: [
            new Assert\Type('int'),
            new Assert\Choice([1, 2, 3, 4, 5, 6, 7]),
        ]
    )]
    #[Assert\NotBlank]
    public array $weekDays;
}
