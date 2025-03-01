<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Routing;

use App\Application\Service\Routing\RoutingMapNodeInterface;
use App\Application\Service\Routing\RoutingMapProviderInterface;
use App\Infrastructure\Routing\RouteMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-type RouteMapNodeArray array{
 *  byKey?: array<string, RouteMapNodeArray[]>,
 *  byRequiredParam?: RouteMapNodeArray[],
 *  matchingAll?: RouteMapNodeArray[],
 *  routeId?: int|null
 * }
 * 
 * @phpstan-ignore typeAlias.circular
 */
final class RouteMatcherTest extends TestCase
{
    /**
     * @param array<int, RouteMapNodeArray> $map
     *
     * @phpstan-ignore missingType.iterableValue
     */
    #[DataProvider('getTestCases')]
    public function testUrlMatching(string $urlPath, array $map, ?int $expectedRouteId): void
    {
        $routeNodesByPriority = [];

        foreach ($map as $priority => $routeNodeArr) {
            $routeNodesByPriority[$priority] = $this->createRouteMapNodeMock($routeNodeArr);
        }

        $routingMapProviderMock = $this->createMock(RoutingMapProviderInterface::class);

        $routingMapProviderMock->expects($this->once())
                            ->method('getRoutingMap')
                            ->willReturn($routeNodesByPriority);

        $matcher = new RouteMatcher($routingMapProviderMock);

        $routeId = $matcher->findMatchingRouteIdForUrl($urlPath);

        $this->assertEquals($expectedRouteId, $routeId);
    }

    /**
     * @return array<int,array{
     *  urlPath: string,
     *  map: array<int, RouteMapNodeArray>,
     *  expectedRouteId: int|null
     * }>
     * 
     * @phpstan-ignore missingType.iterableValue
     */
    public static function getTestCases(): array
    {
        return [
            [
                'urlPath' => '/foo',
                'map' => [
                    0 => [
                        'byKey' => [],
                        'byRequiredParam' => [],
                        'matchingAll' => [
                            [
                                'routeId' => 1
                            ]
                        ],
                    ],
                ],
                'expectedRouteId' => 1,
            ],
            [
                'urlPath' => '/foo',
                'map' => [
                    0 => [
                        'byKey' => [
                            'foo' => [
                                [
                                    'routeId' => 1
                                ],                                
                            ],
                        ],
                    ],
                ],
                'expectedRouteId' => 1,
            ],         
            [
                'urlPath' => '/foo',
                'map' => [
                    0 => [
                        'byRequiredParam' => [
                            [
                                'routeId' => 2
                            ], 
                        ],
                    ],
                ],
                'expectedRouteId' => 2,
            ],  
            [
                'urlPath' => '/foo/1',
                'map' => [
                    0 => [
                        'byKey' => [
                            'foo' => [
                                [
                                    'byRequiredParam' => [
                                        [
                                            'routeId' => 2
                                        ], 
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
                'expectedRouteId' => 2,
            ],                         
            [
                'urlPath' => '/foo/1',
                'map' => [
                    1 => [
                        'byKey' => [
                            'bar' => [
                                [
                                    'byRequiredParam' => [
                                        [
                                            'routeId' => 2
                                        ], 
                                    ],
                                ]
                            ],
                        ],
                    ],                    
                    0 => [
                        'byKey' => [
                            'foo' => [
                                [
                                    'byRequiredParam' => [
                                        [
                                            'routeId' => 1
                                        ], 
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
                'expectedRouteId' => 1,
            ],                        
        ];
    }

    /**
     * @param RouteMapNodeArray $routeNodeArr
     * 
     * @phpstan-ignore missingType.iterableValue
     */
    private function createRouteMapNodeMock(array $routeNodeArr): RoutingMapNodeInterface & MockObject
    {
        $nodeMock = $this->createMock(RoutingMapNodeInterface::class);

        $byKeyNodes = [];

        if (!empty($routeNodeArr['byKey'])) {
            foreach ($routeNodeArr['byKey'] as $key => $childNodeArrs) {
                foreach ($childNodeArrs as $childNodeArr) {
                    $byKeyNodes[$key][] = $this->createRouteMapNodeMock($childNodeArr);
                }
            }
        }

        $nodeMock->expects($this->any())
                ->method('getNodesByKey')
                ->willReturnCallback(function(string $routePartKey) use(&$byKeyNodes): array {
                    return $byKeyNodes[$routePartKey] ?? [];
                });

        $byRequiredParam = [];

        if (!empty($routeNodeArr['byRequiredParam'])) {
            foreach ($routeNodeArr['byRequiredParam'] as $childNodeArr) {
                $byRequiredParam[] = $this->createRouteMapNodeMock($childNodeArr);
            }
        }

        $nodeMock->expects($this->any())
                ->method('getNodesByRequiredParam')
                ->willReturn($byRequiredParam);

        $matchingAll = [];

        if (!empty($routeNodeArr['matchingAll'])) {
            foreach ($routeNodeArr['matchingAll'] as $childNodeArr) {
                $matchingAll[] = $this->createRouteMapNodeMock($childNodeArr);
            }
        }

        $nodeMock->expects($this->any())
                ->method('getNodesMatchingAll')
                ->willReturn($matchingAll);
        
        $terminalRouteId = $routeNodeArr['routeId'] ?? null;

        $nodeMock->expects($this->any())
                ->method('getTerminalRouteId')
                ->willReturn($terminalRouteId);

        return $nodeMock;
    }    
}