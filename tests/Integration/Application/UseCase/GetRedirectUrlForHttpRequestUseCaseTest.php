<?php

declare(strict_types=1);

namespace Tests\Integration\Application\UseCase;

use App\Application\Dto\GeoDto;
use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\MachingRouteIsNotFoundException;
use App\Application\Service\Detector\GeoIpDetectorInterface;
use App\Application\UseCase\GetRedirectUrlForHttpRequestUseCase;
use App\Domain\Entity\Condition;
use App\Domain\Entity\Redirect;
use App\Domain\Entity\Route;
use App\Infrastructure\Service\Detector\GeoIpDetectorApiAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DateTimeImmutable;
use Throwable;

final class GetRedirectUrlForHttpRequestUseCaseTest extends KernelTestCase
{
    private ContainerInterface $container;
    private ?EntityManagerInterface $entityManager;

    /**
     * @param Route[] $routes
     */
    #[DataProvider('getTestCases')]
    public function testCorrectRedirectResolving(
        array $routes, 
        HttpRequestDto $httpRequestDto, 
        ?string $expectedRedirectUrl, 
        ?string $expectedException
    ): void {
        foreach ($routes as $route) {
            $this->entityManager->persist($route);
        }

        $this->entityManager->flush();

        $getRedirectUrlForHttpRequestUseCase = $this->container->get(GetRedirectUrlForHttpRequestUseCase::class);

        if (null !== $expectedException) {
            $this->expectException($expectedException);
        }

        $redirecUrl = ($getRedirectUrlForHttpRequestUseCase)($httpRequestDto);

        if (null === $expectedException) {
            $this->assertEquals($expectedRedirectUrl, $redirecUrl);
        }
    }

    /**
     * @return array{routes: Route[], httpRequestDto: HttpRequestDto, expectedRedirectUrl: string|null, expectedException: class-string<Throwable>|null}[]
     */
    public static function getTestCases(): array
    {
        return [
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/test10'))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test10',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [],
                'httpRequestDto' => new HttpRequestDto(
                    '/test10',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => null,
                'expectedException' => MachingRouteIsNotFoundException::class,  
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/test/foo/bar', 0))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r1@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                    (function(): Route {
                        $route = (new Route('/test/*', 1))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r2@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r2@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/*'))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/*', 0, false))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => null,
                'expectedException' => MachingRouteIsNotFoundException::class,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/test/foo', 10))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                    (function(): Route {
                        $route = (new Route('/a', 1000))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r2@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                    (function(): Route {
                        $route = (new Route('/test/{foo_param}/{bar_param}'))
                                    ->setInitialStep(
                            (new Redirect())
                                            ->setSchemeType('redirect')
                                            ->setSchemeProps(['url' => 'r3@test.com'])
                                    );
    
                        $route->getInitialStep()->setRoute($route);         
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable(),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r3@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/*'))
                                    ->setInitialStep(
                                        (new Condition())
                                            ->setSchemeType('datetime_range')
                                            ->setSchemeProps([
                                                'from' => '2024-05-10 12:00:00', 
                                                'to' => '2024-09-12 00:00:00',
                                            ])
                                            ->setOnPassStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r@test.com'])
                                            )->setOnDeclineStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r2@test.com'])
                                            )
                                    );
    
                        $route->getInitialStep()->setRoute($route);       
                        $route->getInitialStep()->getOnPassStep()->setRoute($route);
                        $route->getInitialStep()->getOnDeclineStep()->setRoute($route);
                        $route->addStep($route->getInitialStep());
                        $route->addStep($route->getInitialStep()->getOnPassStep());
                        $route->addStep($route->getInitialStep()->getOnDeclineStep());
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable('2024-12-12 14:00:00'),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r2@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/*'))
                                    ->setInitialStep(
                                        (new Condition())
                                            ->setSchemeType('datetime_range')
                                            ->setSchemeProps([
                                                'from' => '2024-05-10 12:00:00', 
                                                'to' => '2024-09-12 00:00:00',
                                            ])
                                            ->setOnPassStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r@test.com'])
                                            )->setOnDeclineStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r2@test.com'])
                                            )
                                    );
    
                        $route->getInitialStep()->setRoute($route);       
                        $route->getInitialStep()->getOnPassStep()->setRoute($route);
                        $route->getInitialStep()->getOnDeclineStep()->setRoute($route);
                        $route->addStep($route->getInitialStep());
                        $route->addStep($route->getInitialStep()->getOnPassStep());
                        $route->addStep($route->getInitialStep()->getOnDeclineStep());
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable('2024-07-12 14:00:00'),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r@test.com',
                'expectedException' => null,
            ],
            [
                'routes' => [
                    (function(): Route {
                        $route = (new Route('/*'))
                                    ->setInitialStep(
                                        (new Condition())
                                            ->setSchemeType('country')
                                            ->setSchemeProps([
                                                'countries' => [
                                                    'France',
                                                    'Italy'
                                                ]
                                            ])
                                            ->setOnPassStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r@test.com'])
                                            )->setOnDeclineStep(
                                                (new Redirect())
                                                    ->setSchemeType('redirect')
                                                    ->setSchemeProps(['url' => 'r2@test.com'])
                                            )
                                    );
    
                        $route->getInitialStep()->setRoute($route);       
                        $route->getInitialStep()->getOnPassStep()->setRoute($route);
                        $route->getInitialStep()->getOnDeclineStep()->setRoute($route);
                        $route->addStep($route->getInitialStep());
                        $route->addStep($route->getInitialStep()->getOnPassStep());
                        $route->addStep($route->getInitialStep()->getOnDeclineStep());
                        
                        return $route;
                    })(),
                ],
                'httpRequestDto' => new HttpRequestDto(
                    '/test/foo/bar',
                    'en',
                    [],
                    new DateTimeImmutable('2024-07-12 14:00:00'),
                    '252.99.86.106',
                ),
                'expectedRedirectUrl' => 'r@test.com',
                'expectedException' => null,
            ],
        ];
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->container = static::getContainer();       

        $geoIpDetectorApiAdapterMock = $this->createMock(GeoIpDetectorInterface::class);

        $geoIpDetectorApiAdapterMock->expects($this->any())
                                    ->method('detectGeoByIp')
                                    ->willReturn(new GeoDto(
                                        'Europe',
                                        'France',
                                        'Paris',
                                        48.864716,
                                        2.349014,
                                        'Europe/Paris',
                                    ));

        $this->container->set(GeoIpDetectorApiAdapter::class, $geoIpDetectorApiAdapterMock);

        $this->entityManager = $this->container->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        $this->entityManager->close();
        $this->entityManager = null;
    }    

}