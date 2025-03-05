<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RequestUrlParamsExtractorInterface;

final class RequestUrlParamsExtractor implements RequestUrlParamsExtractorInterface
{
    public function extractParams(string $requestPath, string $routeMask): array
    {
        $params = [];

        $requestPathParts = explode('/', $requestPath);
        $routeMaskParts = explode('/', $routeMask);

        foreach ($routeMaskParts as $i => $routeMaskPart) {
            if (str_starts_with($routeMaskPart, '{') && str_ends_with($routeMaskPart, '}')) {
                $paramName = trim($routeMaskPart, '{}');
                $params[$paramName] = $requestPathParts[$i];
            }
        }

        return $params;
    }
}
