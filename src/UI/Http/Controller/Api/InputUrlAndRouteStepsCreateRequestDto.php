<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class InputUrlAndRouteStepsCreateRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 2048)]
        public readonly string $urlPattern,
        public readonly int $priority,
        public readonly bool $isActive,
        public readonly RouteStepRequestDtoPartial $initialRouteStep,
    ) {
    }
}