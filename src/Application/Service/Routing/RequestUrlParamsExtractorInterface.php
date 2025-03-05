<?php

declare(strict_types=1);

namespace App\Application\Service\Routing;

interface RequestUrlParamsExtractorInterface
{
    /**
     * @return array<string,mixed>
     */
    public function extractParams(string $requestPath, string $routeMask): array;
}
