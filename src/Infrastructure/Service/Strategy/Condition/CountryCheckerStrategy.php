<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\CountryConditionScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Detector\GeoIpDetectorInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CountryCheckerStrategy extends ConditionCheckerStrategy
{
    public function __construct(
        RoutingStepClassRegistryInterface $routingStepClassRegistry,
        RoutingStepSchemeClassRegistryInterface $routingStepSchemeClassRegistry,
        DenormalizerInterface $denormalizer,
        private readonly GeoIpDetectorInterface $geoIpDetector,
    ) {
        parent::__construct($routingStepClassRegistry, $routingStepSchemeClassRegistry, $denormalizer);
    }

    /**
     * @param CountryConditionScheme $routingStepScheme
     */
    protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool {
        $geoDto = $this->geoIpDetector->detectGeoByIp($httpRequestDto->ip);

        return  in_array($geoDto->country, $routingStepScheme->countries);
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof CountryConditionScheme;
    }
}
