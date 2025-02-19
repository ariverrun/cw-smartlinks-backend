<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class RouteStepNestedDto
{
    public function __construct(
        public readonly ?RouteStepNestedDto $onPassStep,
        public readonly ?RouteStepNestedDto $onDeclineStep,
        #[Assert\NotBlank]
        public readonly string $schemeType,
        #[Assert\Type('array')]
        #[Assert\All(
            constraints: [
                new Assert\Type('string')
            ]
        )]
        public readonly array $schemeProps,
    ) {
    }
}
