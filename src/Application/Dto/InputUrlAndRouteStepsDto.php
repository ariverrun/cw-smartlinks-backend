<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class InputUrlAndRouteStepsDto
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 2048)]
        public string $urlPattern,
        public int $priority,
        public bool $isActive,
        public RouteStepNestedDto $initialRouteStep,
    ) {
    }
}