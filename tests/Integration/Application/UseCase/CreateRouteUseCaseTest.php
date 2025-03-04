<?php

declare(strict_types=1);

namespace Tests\Integration\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Exception\DuplicateRouteUrlPatternException;
use App\Application\Exception\UnknowRoutingStepTypeException;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\UseCase\CreateRouteUseCase;
use App\Domain\Entity\RoutingStepInterface;
use App\Domain\Exception\InvalidEntityException;
use App\Domain\Repository\RouteRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

final class CreateRouteUseCaseTest extends KernelTestCase
{
    private RoutingStepClassRegistryInterface $routingStepClassRegistry;

    /**
     * @param class-string<Throwable> | null $exceptionClass
     */
    #[DataProvider('getTestCases')]
    public function testCreatesRouteAndStepsRight(RouteDto $routeDto, ?string $exceptionClass): void
    {
        self::bootKernel();

        $container = static::getContainer();        

        $createRouteUseCase = $container->get(CreateRouteUseCase::class);

        if (null !== $exceptionClass) {
            $this->expectException($exceptionClass);
        }

        $routeId = ($createRouteUseCase)($routeDto);

        if (null === $exceptionClass) {
            $this->assertGreaterThan(0, $routeId);

            /** @var RouteRepositoryInterface $routeRepository */
            $routeRepository = $container->get(RouteRepositoryInterface::class);

            $route = $routeRepository->findOneById($routeId);

            $this->assertEquals($routeDto->urlPattern, $route->getUrlPattern());
            $this->assertEquals($routeDto->priority, $route->getPriority());
            $this->assertEquals($routeDto->isActive, $route->isActive());

            $this->routingStepClassRegistry = $container->get(RoutingStepClassRegistryInterface::class);

            $this->doRouteStepAssertions($route->getInitialStep(), $routeDto->initialStep);
        }
    }

    private function doRouteStepAssertions(RoutingStepInterface $routingStep, RoutingStepNestedDto $routingStepDto): void
    {
        $this->assertEquals($routingStepDto->schemeType, $routingStep->getSchemeType());
        $this->assertEqualsCanonicalizing($routingStepDto->schemeProps, $routingStep->getSchemeProps());

        $this->assertInstanceOf(
            $this->routingStepClassRegistry->getRoutingStepClassByAlias($routingStepDto->type),
            $routingStep,
        );

        if (null !== $routingStepDto->onPassStep) {
            $this->assertNotNull($routingStep->getOnPassStep());
            $this->doRouteStepAssertions($routingStep->getOnPassStep(), $routingStepDto->onPassStep);
        } else {
            $this->assertNull($routingStep->getOnPassStep());
        }

        if (null !== $routingStepDto->onDeclineStep) {
            $this->assertNotNull($routingStep->getOnDeclineStep());
            $this->doRouteStepAssertions($routingStep->getOnDeclineStep(), $routingStepDto->onDeclineStep);
        } else {
            $this->assertNull($routingStep->getOnDeclineStep());
        }        
    }

    /**
     * @return array{routeDto: RouteDto, exceptionClass: class-string<Throwable>|null}[]
     */
    public static function getTestCases(): array
    {
        return [
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test',
                    0,
                    true,
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red1@test.com'],
                        ),
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red2@test.com'],
                        ),
                        'condition',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => null,
            ],
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test2',
                    1,
                    true,
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red1@test.com'],
                        ),
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red2@test.com'],
                        ),
                        'unknown_type',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => UnknowRoutingStepTypeException::class,
            ],
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test',
                    0,
                    true,
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red1@test.com'],
                        ),
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red2@test.com'],
                        ),
                        'condition',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => DuplicateRouteUrlPatternException::class,
            ],
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test3',
                    5,
                    false,
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red1@test.com'],
                        ),
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red2@test.com'],
                        ),
                        'condition',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => null,
            ],
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test4',
                    5,
                    true,
                    new RoutingStepNestedDto(
                        null,
                        null,
                        'condition',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => InvalidEntityException::class,
            ],
            [
                'routeDto' => new RouteDto(
                    null,
                    '/test5',
                    5,
                    false,
                    new RoutingStepNestedDto(
                        new RoutingStepNestedDto(
                            null,
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red1@test.com'],
                        ),
                        new RoutingStepNestedDto(
                            new RoutingStepNestedDto(
                                null,
                                null,
                                'redirect',
                                'redirect',
                                ['url' => 'red2@test.com'],
                            ),
                            null,
                            'redirect',
                            'redirect',
                            ['url' => 'red2@test.com'],
                        ),
                        'condition',
                        'week_day',
                        [
                            'weekDays' => [1,2,3],
                        ],
                    ),
                ),
                'exceptionClass' => InvalidEntityException::class,
            ],
        ];
    }    
}