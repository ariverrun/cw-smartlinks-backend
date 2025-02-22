<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;

interface CreateRouteUseCaseInterface
{
    public function __invoke(RouteDto $dto): int;
}
