<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\HttpRequestDto;

class GetRedirectUrlForHttpRequestUseCase implements GetRedirectUrlForHttpRequestUseCaseInterface
{
    public function __invoke(HttpRequestDto $dto): string
    {
        return '';
    }
}
