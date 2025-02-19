<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Service\Factory\RouteStepFactoryInterface;
use App\Application\Dto\InputUrlAndRouteStepsDto;
use App\Domain\Entity\InputUrl;
use App\Domain\Repository\InputUrlRepositoryInterface;

final class CreateInputUrlAndRouteStepsUseCase implements CreateInputUrlAndRouteStepsUseCaseInterface
{
    public function __construct(
        private readonly RouteStepFactoryInterface $routeStepFactory,
        private readonly InputUrlRepositoryInterface $inputUrlRepository,
    ) {
    }

    public function __invoke(InputUrlAndRouteStepsDto $dto): int
    {
        $inputUrl = new InputUrl($dto->urlPattern, $dto->priority, $dto->isActive);

        $inputUrl->setInitialRouteStep(
            $this->routeStepFactory->createRouteStep($dto->initialRouteStep, $inputUrl),
        );

        $this->inputUrlRepository->save($inputUrl);

        return (int)$inputUrl->getId();
    }
}
