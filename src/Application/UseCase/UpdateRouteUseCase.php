<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Service\Factory\RoutingStepFactoryInterface;
use App\Domain\Repository\RouteRepositoryInterface;

final class UpdateRouteUseCase implements UpdateRouteUseCaseInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly RoutingStepFactoryInterface $routingStepFactory,
    ) {
    }

    public function __invoke(int $routeId, RouteDto $dto): void
    {
        $route = $this->routeRepository->findOneById($routeId);

        $route->setInitialStep(
            $this->routingStepFactory->createRoutingStep($dto->initialStep, $route),
        );

        $this->routeRepository->save($route);
    }
}
