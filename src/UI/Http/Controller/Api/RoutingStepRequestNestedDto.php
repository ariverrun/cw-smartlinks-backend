<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class RoutingStepRequestNestedDto
{
    public function __construct(
        public readonly ?RoutingStepRequestNestedDto $onPassStep,
        public readonly ?RoutingStepRequestNestedDto $onDeclineStep,
        #[Assert\NotBlank]
        public readonly string $type,
        #[Assert\NotBlank]
        public readonly string $schemeType,
        #[Assert\Type('array')]
        #[Assert\All(
            constraints: [
                new Assert\Type('string'),
            ]
        )]
        public readonly array $schemeProps,
    ) {
    }
}
