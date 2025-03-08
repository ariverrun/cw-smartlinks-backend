<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Domain\Enum\RoutingStepType;
use Symfony\Component\Validator\Constraints as Assert;

#[RoutingStepScheme(type: RoutingStepType::CONDITION->value, alias: 'locale')]
final class LocaleConditionScheme implements RoutingStepSchemeInterface
{
    /**
     * @var string[]
     */
    #[Assert\Type('array')]
    #[Assert\All(
        constraints: [
            new Assert\Type('string'),
        ]
    )]
    #[Assert\NotBlank]
    public array $locales;
}
