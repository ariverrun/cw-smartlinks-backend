<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class InputUrlAndRouteStepsDto
{
    public function __construct(
        public readonly ?int $id,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 2048)]
        public readonly string $urlPattern,
        public readonly int $priority,
        public readonly bool $isActive,
        public readonly RouteStepNestedDto $initialRouteStep,
    ) {
    }
}