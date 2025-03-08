<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RoutingStepSchemeCompilerPass implements CompilerPassInterface
{
    public const ROUTING_STEP_SCHEME_TAG = 'app.routing_step_scheme';

    public function process(ContainerBuilder $container)
    {
        $routingStepSchemes = $container->findTaggedServiceIds(self::ROUTING_STEP_SCHEME_TAG);

        $routeStepSchemeClassesByAliases = [];

        foreach ($routingStepSchemes as $className => $classParameters) {
            $routeStepSchemeFullAlias = $classParameters[0]['type'] . '.' . $classParameters[0]['alias'];
            $routeStepSchemeClassesByAliases[$routeStepSchemeFullAlias] = $className;

            $container->removeDefinition($className);
        }

        $container->setParameter('routing_step_scheme_classes_by_aliases', $routeStepSchemeClassesByAliases);
    }
}
