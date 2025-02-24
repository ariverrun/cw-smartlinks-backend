<?php

declare(strict_types=1);

namespace App\Application\Service\Mapper;

interface AutoMapperInterface
{
    /**
     * @param mixed[]|object $source
     * @param mixed[]|object $target
     *
     * @return mixed[]|object|null
     */
    public function map(array | object $source, string | array | object $target): array | object | null;
}
