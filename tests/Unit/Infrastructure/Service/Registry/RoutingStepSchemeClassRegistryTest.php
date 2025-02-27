<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Registry;

use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Exception\UnknownRoutingStepSchemeException;
use App\Infrastructure\Service\Registry\RoutingStepSchemeClassRegistry;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class RoutingStepSchemeClassRegistryTest extends TestCase
{
    public function testThatFindsRightSchemeClass(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepSchemeInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepSchemeInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepSchemeClassRegistry($classesByAlias);

        foreach ($classesByAlias as $alias => $className) {
            $schemeClass = $registry->getRoutingStepSchemeClassByAlias($alias);
            
            $this->assertEquals($className, $schemeClass);
        }
    }

    public function testThatsThrowsExceptionOnInvalidClassConfig(): void
    {      
        $classesByAlias = [
            'a' => $this->createMock(RoutingStepSchemeInterface::class)::class,
            'b' => (new class() {})::class,
            'c' => $this->createMock(RoutingStepSchemeInterface::class)::class,
        ];

        $this->expectException(InvalidArgumentException::class);

        new RoutingStepSchemeClassRegistry($classesByAlias);
    }

    public function testThatThrowsExceptionOnUnknownAlias(): void
    {
        $classesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        foreach ($aliases as $alias) {
            $mock = $this->getMockBuilder(RoutingStepSchemeInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepSchemeInterface_'.$alias . __LINE__)
                            ->getMock();
            $classesByAlias[$alias] = $mock::class;
        }

        $registry = new RoutingStepSchemeClassRegistry($classesByAlias);
        
        $this->expectException(UnknownRoutingStepSchemeException::class);

        $registry->getRoutingStepSchemeClassByAlias('d');
    }
}