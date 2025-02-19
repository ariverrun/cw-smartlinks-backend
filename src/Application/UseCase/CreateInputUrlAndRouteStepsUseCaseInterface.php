<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\InputUrlAndRouteStepsDto;

interface CreateInputUrlAndRouteStepsUseCaseInterface
{
    public function __invoke(InputUrlAndRouteStepsDto $dto): int;
}
