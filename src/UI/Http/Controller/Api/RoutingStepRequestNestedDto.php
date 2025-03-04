<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use App\UI\Http\Validator\ContainsRoutingStepScheme;
use Symfony\Component\Validator\Constraints as Assert;

final class RoutingStepRequestNestedDto
{
    /**
     * @param array<string,mixed> $schemeProps
     */
    public function __construct(
        #[ContainsRoutingStepScheme]
        #[Assert\Valid]
        public readonly ?RoutingStepRequestNestedDto $onPassStep,
        #[ContainsRoutingStepScheme]
        #[Assert\Valid]
        public readonly ?RoutingStepRequestNestedDto $onDeclineStep,
        #[Assert\NotBlank]
        public readonly string $type,
        #[Assert\NotBlank]
        public readonly string $schemeType,
        #[Assert\Type('array')]
        public readonly array $schemeProps,
    ) {
    }
}
