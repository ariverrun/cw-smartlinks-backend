<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Compiler;

use App\Domain\Entity\RoutingStep;
use App\Infrastructure\Exception\CompilerPassException;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ReflectionClass;

final class RoutingStepClassCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $reflection = new ReflectionClass(RoutingStep::class);

        $attributes = $reflection->getAttributes(DiscriminatorMap::class);

        $attribute = array_shift($attributes);

        if (null === $attribute) {
            throw new CompilerPassException('Discriminator map attribute for ' . RoutingStep::class . ' class is not found');
        }

        $arguments = $attribute->getArguments();

        if (empty($arguments)) {
            throw new CompilerPassException('Discriminator map attribute for ' . RoutingStep::class . ' class has no arguments');
        }

        $routingStepClassesByAlias = array_shift($arguments);

        $container->setParameter('routing_step_classes_by_alias', $routingStepClassesByAlias);
    }
}
