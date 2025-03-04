<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;

interface UpdateRouteUseCaseInterface
{
    public function __invoke(int $routeId, RouteDto $dto): void;
}
