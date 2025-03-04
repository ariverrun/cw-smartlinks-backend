<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RoutingStepNestedDto
{
    /**
     * @param array<string,mixed> $schemeProps
     */
    public function __construct(
        public ?RoutingStepNestedDto $onPassStep,
        public ?RoutingStepNestedDto $onDeclineStep,
        #[Assert\NotBlank]
        public string $type,
        #[Assert\NotBlank]
        public string $schemeType,
        #[Assert\Type('array')]
        public array $schemeProps,
    ) {
    }
}
