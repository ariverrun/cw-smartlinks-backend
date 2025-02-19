<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Service\Registry\RouteStepClassRegistryInterface;
use App\Application\Service\Registry\RouteStepSchemeClassRegistryInterface;
use App\Application\Dto\InputUrlAndRouteStepsDto;

final class CreateInputUrlAndRouteStepsUseCase implements CreateInputUrlAndRouteStepsUseCaseInterface
{
    public function __construct(
        private readonly RouteStepClassRegistryInterface $routeStepClassRegistry,
        private readonly RouteStepSchemeClassRegistryInterface $routeStepSchemeClassRegistry,
    ) {
    }

    public function __invoke(InputUrlAndRouteStepsDto $dto): int
    {
dd($dto);        
        return 0;
    }
}