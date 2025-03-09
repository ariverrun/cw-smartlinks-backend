<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Routing;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\RedirectUrlIsNotResolvedException;
use App\Application\Service\Handler\RoutingStepHandlerInterface;
use App\Application\Service\Handler\RoutingStepHandlerResultInterface;
use App\Application\Service\Routing\RequestUrlParamsExtractorInterface;
use App\Domain\Entity\RouteInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Domain\Repository\RouteRepositoryInterface;
use App\Infrastructure\Routing\RedirectUrlResolver;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use RuntimeException;

final class RedirectUrlResolverTest extends TestCase
{
    public function testThrowsExceptionOnRouteNotFound(): void
    {
        $routeRepositoryMock = $this->createMock(RouteRepositoryInterface::class);

        $routeRepositoryMock->expects($this->once())
                            ->method('findOneById')
                            ->willReturn(null);

        $redirectUrlResolver = new RedirectUrlResolver(
            $routeRepositoryMock,
            $this->createMock(RoutingStepHandlerInterface::class),
            $this->createMock(RequestUrlParamsExtractorInterface::class),
        );

        $this->expectException(RuntimeException::class);

        $redirectUrlResolver->resolveRedirectUrl(1, $this->createMock(HttpRequestDto::class));
    }

    public function testThrowsExceptionOnRedirectUrlNotResolved(): void
    {
        $routeMock = $this->createMock(RouteInterface::class);

        $routingStepMock = $this->createMock(RoutingStepInterface::class);

        $routeMock->expects($this->once())
                            ->method('getInitialStep')
                            ->willReturn($routingStepMock);        

        $routeRepositoryMock = $this->createMock(RouteRepositoryInterface::class);

        $routeRepositoryMock->expects($this->once())
                            ->method('findOneById')
                            ->willReturn($routeMock);

        $routingStepHandlerMock = $this->createMock(RoutingStepHandlerInterface::class);

        $resultMock = $this->createMock(RoutingStepHandlerResultInterface::class);

        $resultMock->expects($this->once())
                            ->method('getNextStep')
                            ->willReturn(null);

        $resultMock->expects($this->once())
                            ->method('getRedirectUrl')
                            ->willReturn(null);                            

        $routingStepHandlerMock->expects($this->once())
                            ->method('handleRoutingStep')
                            ->willReturn($resultMock);        

        $redirectUrlResolver = new RedirectUrlResolver(
            $routeRepositoryMock,
            $routingStepHandlerMock,
            $this->createMock(RequestUrlParamsExtractorInterface::class),
        );

        $this->expectException(RedirectUrlIsNotResolvedException::class);

        $httpRequestDto = new HttpRequestDto(
            '',
            '',
            [],
            new DateTimeImmutable(),
            '',
        );

        $redirectUrlResolver->resolveRedirectUrl(1, $httpRequestDto);        
    }
}