<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Exception\DuplicateRouteUrlPatternException;
use App\Application\Exception\RouteIsNotFoundException;
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

        if (null === $route) {
            throw new RouteIsNotFoundException();
        }

        if ($route->getUrlPattern() !== $dto->urlPattern) {
            $sameUrlPattern = $this->routeRepository->findOneByUrlPattern($dto->urlPattern);

            if (
                null !== $sameUrlPattern
                && $route->getId() !== $sameUrlPattern->getId()
            ) {
                throw new DuplicateRouteUrlPatternException();
            }
        }

        $route
            ->setUrlPattern($dto->urlPattern)
            ->setPriority($dto->priority)
            ->setIsActive($dto->isActive)
            ->setInitialStep(
                $this->routingStepFactory->createRoutingStep($dto->initialStep, $route),
            );

        $this->routeRepository->save($route);
    }
}
