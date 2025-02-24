<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Strategy;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Application\Service\Strategy\RoutingStepStrategyInterface;
use App\Domain\Entity\RoutingStepInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use RuntimeException;

abstract class RoutingStepStrategy implements RoutingStepStrategyInterface
{
    public function __construct(
        private readonly RoutingStepClassRegistryInterface $routingStepClassRegistry,
        private readonly RoutingStepSchemeClassRegistryInterface $routingStepSchemeClassRegistry,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    final public function doHandleRoutingStep(
        RoutingStepInterface $routingStep,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface {

        $schemeClass = $this->routingStepSchemeClassRegistry->getRoutingStepSchemeClassByAlias(
            $this->routingStepClassRegistry->getAliasForRoutingStepClass($routingStep::class) . '.' . $routingStep->getSchemeType()
        );

        $routingStepScheme = $this->denormalizer->denormalize($routingStep->getSchemeProps(), $schemeClass);

        if (false === $this->isRouteStepSchemeSupported($routingStepScheme)) {
            throw new RuntimeException();
        }

        return $this->doRouteStepTypeSpecificHandling(
            $routingStep,
            $routingStepScheme,
            $httpRequestDto,
            $context,
        );
    }

    abstract protected function doRouteStepTypeSpecificHandling(
        RoutingStepInterface $routingStep,
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): RoutingStepHandlerResultInterface;

    abstract protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool;
}
