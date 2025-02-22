<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Service\Factory\RoutingStepFactoryInterface;
use App\Application\Dto\RouteDto;
use App\Domain\Entity\Route;
use App\Domain\Repository\RouteRepositoryInterface;

final class CreateRouteUseCase implements CreateRouteUseCaseInterface
{
    public function __construct(
        private readonly RoutingStepFactoryInterface $routingStepFactory,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function __invoke(RouteDto $dto): int
    {
        $route = new Route($dto->urlPattern, $dto->priority, $dto->isActive);

        $route->setInitialStep(
            $this->routingStepFactory->createRoutingStep($dto->initialStep, $route),
        );

        $this->routeRepository->save($route);

        return (int)$route->getId();
    }
}
