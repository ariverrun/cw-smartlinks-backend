<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\RedirectUrlIsInvalidException;
use App\Application\Exception\RedirectUrlIsNotResolvedException;

interface RedirectUrlResolverInterface
{
    /**
     * @throws RedirectUrlIsInvalidException
     * @throws RedirectUrlIsNotResolvedException
     */
    public function resolveRedirectUrl(int $routeId, HttpRequestDto $httpRequestDto): string;
}
