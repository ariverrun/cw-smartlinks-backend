<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Compiler;

use App\Infrastructure\Service\Registry\RoutingStepStrategiesRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RoutingStepStrategiesCompilerPass implements CompilerPassInterface
{
    public const STRATEGIES_SUPPORTED_SCHEMES_TAG = 'app.supported_routing_step_scheme';

    public function process(ContainerBuilder $container)
    {
        $routingStepStrategies = $container->findTaggedServiceIds(self::STRATEGIES_SUPPORTED_SCHEMES_TAG);

        $routeStepSchemeClassesByAliases = $container->getParameter('routing_step_scheme_classes_by_aliases');

        $routeStepSchemeAliasesByClasses = array_flip($routeStepSchemeClassesByAliases);

        $registryDefinition = $container->getDefinition(RoutingStepStrategiesRegistry::class);

        foreach ($routingStepStrategies as $strategyId => $strategyParams) {
            $supportedSchemeClass = $strategyParams[0]['class'];

            $schemeAlias = $routeStepSchemeAliasesByClasses[$supportedSchemeClass] ?? null;

            if (null !== $schemeAlias) {
                $registryDefinition->addMethodCall('addStrategy', [
                    $schemeAlias,
                    new Reference($strategyId),
                ]);
            }
        }
    }
}
