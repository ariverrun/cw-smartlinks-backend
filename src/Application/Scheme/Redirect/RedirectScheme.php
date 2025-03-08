<?php

declare(strict_types=1);

namespace App\Application\Scheme\Redirect;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Domain\Enum\RoutingStepType;
use Symfony\Component\Validator\Constraints as Assert;

#[RoutingStepScheme(type: RoutingStepType::REDIRECT->value, alias: 'redirect')]
final class RedirectScheme implements RoutingStepSchemeInterface
{
    #[Assert\NotBlank]
    public string $url;
}
