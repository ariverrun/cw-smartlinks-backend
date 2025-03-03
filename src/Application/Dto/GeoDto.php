<?php

declare(strict_types=1);

namespace App\Application\Dto;

readonly class GeoDto
{
    public function __construct(
        public ?string $continent,
        public ?string $country,
        public ?string $city,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $timeZone,
    ) {
    }
}
