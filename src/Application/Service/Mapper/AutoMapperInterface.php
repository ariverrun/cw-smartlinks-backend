<?php

declare(strict_types=1);

namespace App\Application\Service\Mapper;

interface AutoMapperInterface
{
    public function map(array | object $source, string | array | object $target): array | object | null;
}
