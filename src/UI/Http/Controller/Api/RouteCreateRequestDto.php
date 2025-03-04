<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use App\UI\Http\Validator\ContainsRoutingStepScheme;
use Symfony\Component\Validator\Constraints as Assert;

final class RouteCreateRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 2048)]
        public readonly string $urlPattern,
        public readonly int $priority,
        public readonly bool $isActive,
        #[ContainsRoutingStepScheme]
        #[Assert\Valid]
        public readonly RoutingStepRequestNestedDto $initialStep,
    ) {
    }
}
