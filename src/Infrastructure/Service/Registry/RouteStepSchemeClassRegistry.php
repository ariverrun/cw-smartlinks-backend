<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Registry;

use App\Application\RouteStepScheme\RouteStepSchemeInterface;
use App\Application\Service\Registry\RouteStepSchemeClassRegistryInterface;
use App\Application\Exception\UnknownRouteStepSchemeException;
use \InvalidArgumentException;
use function \is_subclass_of;
use function \sprintf;

class RouteStepSchemeClassRegistry implements RouteStepSchemeClassRegistryInterface
{
    /**
     * @param array<string,string> $classesByAlias
     */
    public function __construct(
        private readonly array $classesByAlias,
    ) {
        foreach ($classesByAlias as $class) {
            if (!is_subclass_of($class, RouteStepSchemeInterface::class)) {
                throw new InvalidArgumentException(sprintf('%s is not %s implementation', $class, RouteStepSchemeInterface::class));
            }
        }        
    }
    
    public function getRouteStepSchemeClassByAlias(string $alias): string
    {
        if (!isset($this->classesByAlias[$alias])) {
            throw new UnknownRouteStepSchemeException(sprintf('Route step scheme type with %s alias is not found', $alias));
        }

        return $this->classesByAlias[$alias];
    }
}
