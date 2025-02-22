<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RoutingStepNestedDto
{
    public function __construct(
        public ?RoutingStepNestedDto $onPassStep,
        public ?RoutingStepNestedDto $onDeclineStep,
        #[Assert\NotBlank]
        public string $type,
        #[Assert\NotBlank]
        public string $schemeType,
        #[Assert\Type('array')]
        #[Assert\All(
            constraints: [
                new Assert\Type('string'),
            ]
        )]
        public array $schemeProps,
    ) {
    }
}
