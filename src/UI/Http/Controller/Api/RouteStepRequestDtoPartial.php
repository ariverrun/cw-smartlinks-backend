<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use Symfony\Component\Validator\Constraints as Assert;

final class RouteStepRequestDtoPartial
{
    public function __construct(
        public readonly ?RouteStepRequestDtoPartial $onPassStep,
        public readonly ?RouteStepRequestDtoPartial $onDeclineStep,
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
