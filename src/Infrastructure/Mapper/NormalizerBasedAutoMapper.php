<?php


declare(strict_types=1);

namespace App\Infrastructure\Mapper;

use App\Application\Service\Mapper\AutoMapperInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizerBasedAutoMapper implements AutoMapperInterface
{
    public function __construct(
        private readonly DenormalizerInterface & NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @param mixed[]|object $source
     * @param mixed[]|object $target
     *
     * @return mixed[]|object|null
     */
    public function map(array | object $source, string | array | object $target): array | object | null
    {
        $arrayData = $this->normalizer->normalize($source);

        $mappedTarget = $this->normalizer->denormalize($arrayData, $target);

        return $mappedTarget;
    }
}
