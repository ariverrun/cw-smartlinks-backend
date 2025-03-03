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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use DateTimeImmutable;

final class DateTimeRangeCheckerStrategyTest extends TestCase
{
    #[DataProvider('getTestCases')]
    public function testConditionChecking(
        DateTimeImmutable $from, 
        DateTimeImmutable $to, 
        DateTimeImmutable $requestTime, 
        bool $meetsCondition
    ): void {
        $nextRoutingStepMock = $this->createMock(RoutingStepInterface::class);

        $methodToGetNextStep = true === $meetsCondition ? 'getOnPassStep' : 'getOnDeclineStep';

        $routingStepMock = $this->createMock(RoutingStepInterface::class);
        $routingStepMock->expects($this->once())
                                ->method($methodToGetNextStep)
                                ->willReturn($nextRoutingStepMock);

        $httpRequestDto = new HttpRequestDto(
            '',
            '',
            [],
            $requestTime,
            '',
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
        $scheme->from = $from;
        $scheme->to = $to;

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

        $this->assertEqualsCanonicalizing($nextRoutingStepMock, $result->getNextStep());
    }

    /**
     * @return array<string, array{
     *   from: DateTimeImmutable, 
     *   to: DateTimeImmutable,
     *   requestTime: DateTimeImmutable,
     *   meetsCondition: bool
     * }>
     */
    public static function getTestCases(): array
    {
        return [
            'in_the_middle_of_range' => [
                'from' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'to' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'requestTime' => new DateTimeImmutable('2025-01-10 12:00:00'),
                'meetsCondition' => true,
            ],
            'before_range' => [
                'from' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'to' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'requestTime' => new DateTimeImmutable('2023-01-10 12:00:00'),
                'meetsCondition' => false,
            ],      
            'after_range' => [
                'from' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'to' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'requestTime' => new DateTimeImmutable('2026-01-10 12:00:00'),
                'meetsCondition' => false,
            ],   
            'at_the_beginning_of_range' => [
                'from' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'to' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'requestTime' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'meetsCondition' => true,
            ],
            'at_the_end_of_range' => [
                'from' => new DateTimeImmutable('2024-02-12 12:00:00'),
                'to' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'requestTime' => new DateTimeImmutable('2025-02-02 12:40:00'),
                'meetsCondition' => false,
            ],                                  
        ];
    }
}