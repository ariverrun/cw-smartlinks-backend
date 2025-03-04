<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Exception\DuplicateRouteUrlPatternException;
use App\Application\Exception\RouteIsNotFoundException;

interface UpdateRouteUseCaseInterface
{
    /**
     * @throws DuplicateRouteUrlPatternException
     * @throws RouteIsNotFoundException
     */
    public function __invoke(int $routeId, RouteDto $dto): void;
}
