<?php

declare(strict_types=1);

namespace App\Application\Service\Factory;

use App\Application\Dto\RouteStepNestedDto;
use App\Domain\Entity\InputUrl;
use App\Domain\Entity\RouteStep;

interface RouteStepFactoryInterface
{
    public function createRouteStep(RouteStepNestedDto $dto, InputUrl $inputUrl): RouteStep;
}
