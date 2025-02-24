<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\DateTimeRangeConditionScheme;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Strategy\Condition\DateTimeRangeCheckerStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use DateTimeImmutable;

final class DateTimeRangeCheckerStrategyTest extends TestCase
{
    public function testWhenInDateRange(): void
    {
        $onPassRoutingStepMock = $this->createMock(RoutingStepInterface::class);

        $routingStepMock = $this->createMock(RoutingStepInterface::class);
        $routingStepMock->expects($this->once())
                                ->method('getOnPassStep')
                                ->willReturn($onPassRoutingStepMock);

        $httpRequestDto = new HttpRequestDto(
            '',
            '',
            [],
            new DateTimeImmutable('2025-01-10 12:00:00'),
        );

        $routingStepClassRegistryMock = $this->createMock(RoutingStepClassRegistryInterface::class);
        $routingStepClassRegistryMock->expects($this->once())
                                ->method('getAliasForRoutingStepClass')
                                ->willReturn('');        

        $routingStepSchemeClassRegistry = $this->createMock(RoutingStepSchemeClassRegistryInterface::class);
        $routingStepSchemeClassRegistry->expects($this->once())
                                ->method('getRoutingStepSchemeClassByAlias')
                                ->willReturn(DateTimeRangeConditionScheme::class);

        $scheme = new DateTimeRangeConditionScheme();
        $scheme->from = new DateTimeImmutable('2024-02-12 12:00:00');
        $scheme->to = new DateTimeImmutable('2025-02-02 12:40:00');

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $denormalizerMock->expects($this->once())
                                ->method('denormalize')
                                ->willReturn($scheme);        

        $strategy = new DateTimeRangeCheckerStrategy(
            $routingStepClassRegistryMock,
            $routingStepSchemeClassRegistry,
            $denormalizerMock
        );

        $result = $strategy->doHandleRoutingStep($routingStepMock, $httpRequestDto, $this->createMock(RedirectionContextInterface::class));

        $this->assertEqualsCanonicalizing($onPassRoutingStepMock, $result->getNextStep());
    }
}