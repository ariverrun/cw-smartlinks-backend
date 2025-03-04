<?php

declare(strict_types=1);

namespace App\Application\Scheme\Condition;

use App\Application\Scheme\RoutingStepSchemeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LocaleConditionScheme implements RoutingStepSchemeInterface
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
