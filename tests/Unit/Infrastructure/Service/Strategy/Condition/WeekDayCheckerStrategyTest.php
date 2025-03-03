<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\WeekDayConditionScheme;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Strategy\Condition\WeekDayCheckerStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use DateTimeImmutable;

final class WeekDayCheckerStrategyTest extends TestCase
{
    /**
     * @param int[] $weekDays
     */
    #[DataProvider('getTestCases')]
    public function testConditionChecking(
        array $weekDays, 
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
                                ->willReturn(WeekDayConditionScheme::class);

        $scheme = new WeekDayConditionScheme();
        $scheme->weekDays = $weekDays;

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $denormalizerMock->expects($this->once())
                                ->method('denormalize')
                                ->willReturn($scheme);        

        $strategy = new WeekDayCheckerStrategy(
            $routingStepClassRegistryMock,
            $routingStepSchemeClassRegistry,
            $denormalizerMock
        );

        $result = $strategy->doHandleRoutingStep($routingStepMock, $httpRequestDto, $this->createMock(RedirectionContextInterface::class));

        $this->assertEqualsCanonicalizing($nextRoutingStepMock, $result->getNextStep());        
    }

    /**
     * @return array<int, array{
     *  weekDays: int[],
     *  requestTime: DateTimeImmutable,
     *  meetsCondition: bool
     * }>
     */
    public static function getTestCases(): array
    {
        return [
            [
                'weekDays' => [1],
                'requestTime' => new DateTimeImmutable('2025-02-03 12:00:00'),
                'meetsCondition' => true,
            ],
            [
                'weekDays' => [1,3,7],
                'requestTime' => new DateTimeImmutable('2025-02-02 12:00:00'),
                'meetsCondition' => true,
            ],    
            [
                'weekDays' => [2],
                'requestTime' => new DateTimeImmutable('2025-02-03 12:00:00'),
                'meetsCondition' => false,
            ],
            [
                'weekDays' => [2,4],
                'requestTime' => new DateTimeImmutable('2025-02-02 12:00:00'),
                'meetsCondition' => false,
            ],                     
        ];
    }
}