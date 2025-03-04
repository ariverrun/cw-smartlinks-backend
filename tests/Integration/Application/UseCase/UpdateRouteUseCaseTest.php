<?php

declare(strict_types=1);

namespace Tests\Integration\Application\UseCase;

use App\Application\Dto\RouteDto;
use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Exception\DuplicateRouteUrlPatternException;
use App\Application\Exception\RouteIsNotFoundException;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\UseCase\UpdateRouteUseCase;
use App\Domain\Entity\Redirect;
use App\Domain\Entity\Route;
use App\Domain\Entity\RoutingStepInterface;
use App\Domain\Repository\RouteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UpdateRouteUseCaseTest extends KernelTestCase
{
    private ContainerInterface $container;

    private ?EntityManagerInterface $entityManager;

    private RoutingStepClassRegistryInterface $routingStepClassRegistry;

    private RouteRepositoryInterface $routeRepository;

    public function testSuccessfulUpdate(): void
    {
        $route = (new Route('/test'))
                    ->setInitialStep(
                        (new Redirect())
                                        ->setSchemeType('redirect')
                                        ->setSchemeProps(['url' => 'r@test.com'])
                    );

        $route->getInitialStep()->setRoute($route);

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        $routeId = $route->getId();

        $routeDto = new RouteDto(
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
        );

        $updateRouteUseCase = $this->container->get(UpdateRouteUseCase::class);

        ($updateRouteUseCase)($routeId, $routeDto);

        $route = $this->routeRepository->findOneById($routeId);

        $this->assertEquals($routeDto->urlPattern, $route->getUrlPattern());
        $this->assertEquals($routeDto->priority, $route->getPriority());
        $this->assertEquals($routeDto->isActive, $route->isActive());

        $this->doRouteStepAssertions($route->getInitialStep(), $routeDto->initialStep);        
    }

    public function testFailOnNotFoundRoute(): void
    {
        $routeId = 100;

        $routeDto = new RouteDto(
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
        );

        $updateRouteUseCase = $this->container->get(UpdateRouteUseCase::class);

        $this->expectException(RouteIsNotFoundException::class);

        ($updateRouteUseCase)($routeId, $routeDto);
    }

    public function testFailOnDuplicate(): void
    {
        $route = (new Route('/test1'))
                    ->setInitialStep(
                        (new Redirect())
                                        ->setSchemeType('redirect')
                                        ->setSchemeProps(['url' => 'r@test.com'])
                    );

        $route->getInitialStep()->setRoute($route);

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        $route = (new Route('/test2'))
                    ->setInitialStep(
                        (new Redirect())
                                        ->setSchemeType('redirect')
                                        ->setSchemeProps(['url' => 'r@test.com'])
                    );

        $route->getInitialStep()->setRoute($route);

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        $routeId = $route->getId();

        $routeDto = new RouteDto(
            null,
            '/test1',
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
        );

        $updateRouteUseCase = $this->container->get(UpdateRouteUseCase::class);

        $this->expectException(DuplicateRouteUrlPatternException::class);

        ($updateRouteUseCase)($routeId, $routeDto);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->container = static::getContainer();       

        $this->entityManager = $this->container->get(EntityManagerInterface::class);

        $this->routeRepository = $this->container->get(RouteRepositoryInterface::class);     
        
        $this->routingStepClassRegistry = $this->container->get(RoutingStepClassRegistryInterface::class);        
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        $this->entityManager->close();

        $this->entityManager = null;
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
}