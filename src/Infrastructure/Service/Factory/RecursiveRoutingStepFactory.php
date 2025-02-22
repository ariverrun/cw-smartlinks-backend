<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Factory;

use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Service\Factory\RoutingStepFactoryInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Domain\Entity\Route;
use App\Domain\Entity\RoutingStep;

class RecursiveRoutingStepFactory implements RoutingStepFactoryInterface
{
    public function __construct(
        private readonly RoutingStepClassRegistryInterface $routingStepClassRegistry,
    ) {
    }

    public function createRoutingStep(RoutingStepNestedDto $dto, Route $route): RoutingStep
    {
        /** @var class-string<RoutingStep> $routingStepClass */
        $routingStepClass = $this->routingStepClassRegistry->getRoutingStepClassByAlias($dto->type);

        $routingStep = (new $routingStepClass())
                            ->setRoute($route)
                            ->setSchemeType($dto->schemeType)
                            ->setSchemeProps($dto->schemeProps);

        if (null !== $dto->onPassStep) {
            $routingStep->setOnPassStep(
                $this->createRoutingStep($dto->onPassStep, $route)
            );
        }

        if (null !== $dto->onDeclineStep) {
            $routingStep->setOnDeclineStep(
                $this->createRoutingStep($dto->onDeclineStep, $route)
            );
        }

        $route->addStep($routingStep);

        return $routingStep;
    }
}
