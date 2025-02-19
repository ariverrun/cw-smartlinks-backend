<?php

declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Application\Exception\InvalidMappingResultException;
use App\Application\Service\Mapper\AutoMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatingAutoMapperDecorator implements AutoMapperInterface
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function map(array | object $source, string | array | object $target): array | object | null
    {
        $mappedTarget = $this->autoMapper->map($source, $target);

        $violationsList = $this->validator->validate($mappedTarget);

        if ($violationsList->count() > 0) {
            throw new InvalidMappingResultException();
        }

        return $mappedTarget;
    }
}
