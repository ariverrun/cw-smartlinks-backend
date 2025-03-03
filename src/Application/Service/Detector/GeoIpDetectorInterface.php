<?php

declare(strict_types=1);

namespace App\Application\Service\Detector;

use App\Application\Dto\GeoDto;
use App\Application\Exception\GeoDetectionFailedException;

interface GeoIpDetectorInterface
{
    /**
     * @throws GeoDetectionFailedException
     */
    public function detectGeoByIp(string $ipAddress): GeoDto;
}
