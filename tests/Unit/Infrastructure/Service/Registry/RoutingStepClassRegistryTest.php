<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Registry;

use App\Application\Exception\UnknowRoutingStepTypeException;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Registry\RoutingStepClassRegistry;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class RoutingStepClassRegistryTest extends TestCase
{
    public function testThatFindsRightRoutingStepClass(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepClassRegistry($classesByAlias);

        foreach ($classesByAlias as $alias => $className) {
            $stepClass = $registry->getRoutingStepClassByAlias($alias);
            
            $this->assertEquals($className, $stepClass);
        }
    }

    public function testThatsThrowsExceptionOnInvalidClassConfig(): void
    {
        $classesByAlias = [
            'a' => $this->createMock(RoutingStepInterface::class)::class,
            'b' => (new class() {})::class,
            'c' => $this->createMock(RoutingStepInterface::class)::class,
        ];

        $this->expectException(InvalidArgumentException::class);
 
        new RoutingStepClassRegistry($classesByAlias);
    }

    public function testThatThrowsExceptionOnUnknownAlias(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepClassRegistry($classesByAlias);
        
        $this->expectException(UnknowRoutingStepTypeException::class);

        $registry->getRoutingStepClassByAlias('d');
    }

    public function testThatFindsRightAlias(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepClassRegistry($classesByAlias);

        /**
         * @var array<string,string>
         */
        $aliasesByClass = array_flip($classesByAlias);

        foreach ($aliasesByClass as $className => $alias) {
            $stepAlias = $registry->getAliasForRoutingStepClass($className);
            
            $this->assertEquals($alias, $stepAlias);
        }
    }

    public function testThatThrowsExceptionOnUnknownStepClass(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepClassRegistry($classesByAlias);
        
        $unknownClassMock = $this->getMockBuilder(RoutingStepInterface::class)
                                /**
                                 * @phpstan-ignore argument.type
                                 */
                                ->setMockClassName('Mock_RoutingStepInterface_unkwnown' . __LINE__)
                                ->getMock();

        $this->expectException(UnknowRoutingStepTypeException::class);

        $registry->getAliasForRoutingStepClass($unknownClassMock::class);
    }
}