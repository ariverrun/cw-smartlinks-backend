<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Registry;

use App\Application\Exception\RoutingStepStrategyIsNotFoundException;
use App\Application\Service\Strategy\RoutingStepStrategyInterface;
use App\Infrastructure\Service\Registry\RoutingStepStrategiesRegistry;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class RoutingStepStrategiesRegistryTest extends TestCase
{
    public function testThatFindsRightStrategy(): void
    {
        $strategiesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        $registry = new RoutingStepStrategiesRegistry();

        foreach ($aliases as $alias) {
            $strategyMock = $this->getMockBuilder(RoutingStepStrategyInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepStrategyInterface_'.$alias . __LINE__)
                            ->getMock();
            $strategiesByAlias[$alias] = $strategyMock;
            $registry->addStrategy($alias, $strategyMock);
        }

        foreach ($strategiesByAlias as $alias => $strategy) {
            $foundStrategy = $registry->getStrategyByAlias($alias);
            
            $this->assertEqualsCanonicalizing($strategy, $foundStrategy);
        }
    }

    public function testThatThrowsExceptionOnUnknownAlias(): void
    {
        $strategiesByAlias = [];

        $aliases = ['a', 'b', 'c'];

        $registry = new RoutingStepStrategiesRegistry();

        foreach ($aliases as $alias) {
            $strategyMock = $this->getMockBuilder(RoutingStepStrategyInterface::class)
                            /**
                             * @phpstan-ignore argument.type
                             */
                            ->setMockClassName('Mock_RoutingStepStrategyInterface_'.$alias . __LINE__)
                            ->getMock();
            $strategiesByAlias[$alias] = $strategyMock;

            $registry->addStrategy($alias, $strategyMock);
        }
        
        $this->expectException(RoutingStepStrategyIsNotFoundException::class);

        $registry->getStrategyByAlias('d');
    }
}