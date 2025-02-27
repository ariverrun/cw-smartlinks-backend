<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Factory;

use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Domain\Entity\RouteInterface;
use App\Domain\Entity\Condition;
use App\Domain\Entity\Redirect;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Factory\RecursiveRoutingStepFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RecursiveRoutingStepFactoryTest extends TestCase
{
    /**
     * @var array<int,int>
     */
    private array $addedStepsIds = [];

    #[DataProvider('getTestCases')]
    public function testThatCreatesStepsCorrectly(RoutingStepNestedDto $routingStepDto): void
    {
        $routingStepClassRegistryMock = $this->createMock(RoutingStepClassRegistryInterface::class);

        $routingStepClassRegistryMock->expects($this->any())
                                ->method('getRoutingStepClassByAlias')
                                ->willReturnCallback(function(string $alias): string {
                                    return $alias;
                                });
        
        $routeMock = $this->createMock(RouteInterface::class);
        $routeMock->expects($this->any())
                                ->method('addStep')
                                ->willReturnCallback(function(RoutingStepInterface $routingStep) use($routeMock): RouteInterface {
                                    $this->addedStepsIds[spl_object_id($routingStep)] = spl_object_id($routingStep);
                                    return $routeMock;
                                });


        $factory = new RecursiveRoutingStepFactory($routingStepClassRegistryMock);

        $initialRoutingStep = $factory->createRoutingStep($routingStepDto, $routeMock);

        $this->doRoutStepAssertions($initialRoutingStep, $routingStepDto);

        $this->assertEmpty($this->addedStepsIds);
    }

    private function doRoutStepAssertions(RoutingStepInterface $routingStep, RoutingStepNestedDto $routingStepDto): void
    {
        $this->assertEquals($routingStepDto->type, $routingStep::class);
        $this->assertEquals($routingStepDto->schemeType, $routingStep->getSchemeType());
        $this->assertEqualsCanonicalizing($routingStepDto->schemeProps, $routingStep->getSchemeProps());
        $this->assertArrayHasKey(spl_object_id($routingStep), $this->addedStepsIds);
        unset($this->addedStepsIds[spl_object_id($routingStep)]);

        if (null !== $routingStepDto->onPassStep) {
            $this->assertNotNull($routingStep->getOnPassStep());
            $this->doRoutStepAssertions($routingStep->getOnPassStep(), $routingStepDto->onPassStep);
        }

        if (null !== $routingStepDto->onDeclineStep) {
            $this->assertNotNull($routingStep->getOnDeclineStep());
            $this->doRoutStepAssertions($routingStep->getOnDeclineStep(), $routingStepDto->onDeclineStep);
        }        
    }
    
    /**
     * @return RoutingStepNestedDto[][]
     */
    public static function getTestCases(): array
    {
        return [
            [
                new RoutingStepNestedDto(
                    new RoutingStepNestedDto(
                        null,
                        null,
                        Redirect::class,
                        'r1',
                        ['r' => 1],
                    ),
                    new RoutingStepNestedDto(
                        null,
                        null,
                        Redirect::class,
                        'r2',
                        ['r' => [true, false]],
                    ),
                    Condition::class,
                    'a',
                    [
                        'a1' => 1,
                    ],
                )
            ],
            [
                new RoutingStepNestedDto(
                    new RoutingStepNestedDto(
                        null,
                        null,
                        Redirect::class,
                        'r1',
                        ['r' => 1],
                    ),
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            Redirect::class,
                            'r1',
                            ['r' => 1],
                        ),
                        new RoutingStepNestedDto(
                            null,
                            null,
                            Redirect::class,
                            'r2',
                            ['r' => [true, false]],
                        ),
                        Condition::class,
                        'a',
                        [
                            'a1' => 1,
                        ],
                    ),
                    Condition::class,
                    'a',
                    [
                        'a1' => 1,
                    ],
                )                
            ]
        ];
    }
}