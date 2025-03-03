<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Detector;

use App\Application\Dto\GeoDto;
use App\Application\Exception\GeoDetectionFailedException;
use App\Application\Service\Detector\GeoIpDetectorInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class GeoIpDetectorApiAdapter implements GeoIpDetectorInterface
{
    public function __construct(
        private readonly ClientInterface $geoIpDetectorApi,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    public function detectGeoByIp(string $ipAddress): GeoDto
    {
        $request = new Request('GET', '/api/v1/geo/?' . http_build_query(['ip' => $ipAddress]));
        $response = $this->geoIpDetectorApi->sendRequest($request);

        $responseData = json_decode((string)$response->getBody(), true);

        if (
            200 !== $response->getStatusCode()
            || !isset($responseData['data'])
            || !is_array($responseData['data'])
        ) {
            throw new GeoDetectionFailedException('Request to geo ip API service failed');
        }

        $geoDto = $this->denormalizer->denormalize($responseData['data'], GeoDto::class);

        return $geoDto;
    }
}
