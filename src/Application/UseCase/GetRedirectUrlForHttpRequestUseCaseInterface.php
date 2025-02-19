<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\HttpRequestDto;

interface GetRedirectUrlForHttpRequestUseCaseInterface
{
    public function __invoke(HttpRequestDto $dto): string;
}
