<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

readonly class HttpRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 2048)]
        public string $requestPath,
        #[Assert\NotBlank]
        #[Assert\Locale(canonicalize: true)]
        public string $locale,
        /**
         * @var array<string,string> $headers
         */
        #[Assert\All([
            new Assert\Type('array'),
            new Assert\All([
                new Assert\Type('string'),
            ]),
        ])]
        public array $headers,
        public DateTimeImmutable $requestTime,
    ) {
    }
}
