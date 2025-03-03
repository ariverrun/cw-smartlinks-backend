<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Detector;

use App\Application\Dto\GeoDto;
use App\Application\Service\Detector\GeoIpDetectorInterface;

final class MemorizingGeoIpDetectorDecorator implements GeoIpDetectorInterface
{
    /**
     * @var array<string, GeoDto>
     */
    private array $geoDtosByIp = [];

    public function __construct(
        private readonly GeoIpDetectorInterface $decoratedDetector,
    ) {
    }

    public function detectGeoByIp(string $ipAddress): GeoDto
    {
        if (!isset($this->geoDtosByIp[$ipAddress])) {
            $this->geoDtosByIp[$ipAddress] = $this->decoratedDetector->detectGeoByIp($ipAddress);
        }

        return $this->geoDtosByIp[$ipAddress];
    }
}
