<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class DateTimeRangeConditionScheme implements RoutingStepSchemeInterface
{
    #[Assert\NotBlank]
    public DateTimeImmutable $from;

    #[Assert\NotBlank]
    public DateTimeImmutable $to;
}
