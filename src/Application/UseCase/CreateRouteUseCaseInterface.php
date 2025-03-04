<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Exception\DuplicateRouteUrlPatternException;

interface CreateRouteUseCaseInterface
{
    /**
     * @throws DuplicateRouteUrlPatternException
     */
    public function __invoke(RouteDto $dto): int;
}
