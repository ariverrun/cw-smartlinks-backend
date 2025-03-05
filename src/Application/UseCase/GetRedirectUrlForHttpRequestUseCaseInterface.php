<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\MachingRouteIsNotFoundException;

interface GetRedirectUrlForHttpRequestUseCaseInterface
{
    /**
     * @throws MachingRouteIsNotFoundException
     */
    public function __invoke(HttpRequestDto $dto): string;
}
