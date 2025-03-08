<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\DependencyInjection\Compiler;

use App\Infrastructure\DependencyInjection\Compiler\RoutingStepStrategiesCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RoutingStepStrategiesCompilerPassTest extends TestCase
{
    public function testCompilerPass(): void
    {
        $containerMock = $this->createMock(ContainerBuilder::class);

        $routingStepStrategiesArr = [
            'strategyA' => [
                [
                    'class' => 'classA',
                ],
            ],
            'strategyB' => [
                [
                    'class' => 'classB',
                ],
            ],
        ];

        $containerMock->expects($this->once())
                ->method('findTaggedServiceIds')
                ->willReturn($routingStepStrategiesArr);

        $routeStepSchemeClassesByAliases = [
            'typeA.a' => 'classA',
            'typeB.b' => 'classB',
        ];

        $containerMock->expects($this->once())
                      ->method('getParameter')
                      ->willReturn($routeStepSchemeClassesByAliases);

        $registryDefinitionMock = $this->createMock(Definition::class);

        $containerMock->expects($this->once())
                        ->method('getDefinition')
                        ->willReturn($registryDefinitionMock);

        $addedStrategies = [];

        $registryDefinitionMock->expects($this->exactly(count($routingStepStrategiesArr)))
                                ->method('addMethodCall')
                                ->willReturnCallback(function(string $method, array $args) use(&$addedStrategies, $registryDefinitionMock): Definition {
                                    $this->assertEquals('addStrategy', $method);
                                    $this->assertCount(2, $args);
                                    $addedStrategies[$args[0]] = $args[1];

                                    return $registryDefinitionMock;
                                });

        $compilerPass = new RoutingStepStrategiesCompilerPass();
        $compilerPass->process($containerMock);

        $this->assertEqualsCanonicalizing(array_keys($routeStepSchemeClassesByAliases), array_keys($addedStrategies));

        $this->assertEquals('strategyA', (string)$addedStrategies['typeA.a']);
        $this->assertEquals('strategyB', (string)$addedStrategies['typeB.b']);

    }
}