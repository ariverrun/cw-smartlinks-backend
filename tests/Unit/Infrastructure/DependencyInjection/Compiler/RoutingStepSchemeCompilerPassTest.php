<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\DependencyInjection\Compiler;

use App\Infrastructure\DependencyInjection\Compiler\RoutingStepSchemeCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RoutingStepSchemeCompilerPassTest extends TestCase
{
    public function testCompilerPass(): void
    {
        $containerMock = $this->createMock(ContainerBuilder::class);
        
        $routingStepSchemesArr = [
            'classA' => [
                [
                    'type' => 'typeA',
                    'alias' => 'a',
                ],
            ],
            'classB' => [
                [
                    'type' => 'typeB',
                    'alias' => 'b',
                ],
            ],
        ];

        $expectedMap = [
            'typeA.a' => 'classA',
            'typeB.b' => 'classB',
        ];

        $containerMock->expects($this->once())
                      ->method('findTaggedServiceIds')
                      ->willReturn($routingStepSchemesArr);

        $containerMock->expects($this->exactly(count($routingStepSchemesArr)))
                        ->method('removeDefinition');

        $containerMock->expects($this->once())
                        ->method('setParameter')
                        ->willReturnCallback(function(string $paramName, array $aliasMap) use($expectedMap): void {
                            $this->assertEquals('routing_step_scheme_classes_by_aliases', $paramName);
                            $this->assertEqualsCanonicalizing($expectedMap, $aliasMap);
                        });

        $compilerPass = new RoutingStepSchemeCompilerPass();
        $compilerPass->process($containerMock);
    }
}