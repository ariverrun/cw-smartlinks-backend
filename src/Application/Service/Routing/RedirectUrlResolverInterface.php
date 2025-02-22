<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

use App\Application\Dto\HttpRequestDto;

interface RedirectUrlResolverInterface
{
    public function resolveRedirectUrl(int $routeId, HttpRequestDto $httpRequestDto): string;
}
