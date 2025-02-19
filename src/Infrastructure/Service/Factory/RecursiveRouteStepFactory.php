<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Factory;

use App\Application\Dto\RouteStepNestedDto;
use App\Application\Service\Factory\RouteStepFactoryInterface;
use App\Application\Service\Registry\RouteStepClassRegistryInterface;
use App\Domain\Entity\InputUrl;
use App\Domain\Entity\RouteStep;

class RecursiveRouteStepFactory implements RouteStepFactoryInterface
{
    public function __construct(
        private readonly RouteStepClassRegistryInterface $routeStepClassRegistry,
    ) {
    }

    public function createRouteStep(RouteStepNestedDto $dto, InputUrl $inputUrl): RouteStep
    {
        /** @var class-string<RouteStep> $routeStepClass */
        $routeStepClass = $this->routeStepClassRegistry->getRouteStepClassByAlias($dto->type);

        $routeStep = (new $routeStepClass())
                            ->setInputUrl($inputUrl)
                            ->setSchemeType($dto->schemeType)
                            ->setSchemeProps($dto->schemeProps);

        if (null !== $dto->onPassStep) {
            $routeStep->setOnPassStep(
                $this->createRouteStep($dto->onPassStep, $inputUrl)
            );
        }

        if (null !== $dto->onDeclineStep) {
            $routeStep->setOnDeclineStep(
                $this->createRouteStep($dto->onDeclineStep, $inputUrl)
            );
        }

        $inputUrl->addRouteStep($routeStep);

        return $routeStep;
    }
}
