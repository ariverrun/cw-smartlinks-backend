<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Redirect\RedirectScheme;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Strategy\Redirect\RedirectStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class RedirectStrategyTest extends TestCase
{
    public function testReturnsRedirectUrlFromScheme(): void
    {
        $expectedUrl = 'test@test.com';

        $routingStepClassRegistryMock = $this->createMock(RoutingStepClassRegistryInterface::class);
        $routingStepClassRegistryMock->expects($this->once())
                                ->method('getAliasForRoutingStepClass')
                                ->willReturn('');        

        $routingStepSchemeClassRegistry = $this->createMock(RoutingStepSchemeClassRegistryInterface::class);
        $routingStepSchemeClassRegistry->expects($this->once())
                                ->method('getRoutingStepSchemeClassByAlias')
                                ->willReturn(RedirectScheme::class);

        $scheme = new RedirectScheme();
        $scheme->url = $expectedUrl;

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $denormalizerMock->expects($this->once())
                                ->method('denormalize')
                                ->willReturn($scheme);      


        $strategy = new RedirectStrategy(
            $routingStepClassRegistryMock,
            $routingStepSchemeClassRegistry,
            $denormalizerMock
        );

        $result = $strategy->doHandleRoutingStep(
            $this->createMock(RoutingStepInterface::class), 
            $this->createMock(HttpRequestDto::class), 
            $this->createMock(RedirectionContextInterface::class)
        );

        $this->assertEqualsCanonicalizing($expectedUrl, $result->getRedirectUrl());
    }
}