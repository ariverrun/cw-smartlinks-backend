<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Domain\Enum\RoutingStepType;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[RoutingStepScheme(type: RoutingStepType::CONDITION->value, alias: 'datetime_range')]
final class DateTimeRangeConditionScheme implements RoutingStepSchemeInterface
{
    #[Assert\NotBlank]
    public DateTimeImmutable $from;

    #[Assert\NotBlank]
    public DateTimeImmutable $to;
}
