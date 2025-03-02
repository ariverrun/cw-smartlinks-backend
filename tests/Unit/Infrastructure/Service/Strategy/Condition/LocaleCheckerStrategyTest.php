<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Strategy\Condition;

use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\Condition\LocaleConditionScheme;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Application\Service\Registry\RoutingStepClassRegistryInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\Domain\Entity\RoutingStepInterface;
use App\Infrastructure\Service\Strategy\Condition\LocaleCheckerStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use DateTimeImmutable;

final class LocaleCheckerStrategyTest extends TestCase
{
    /**
     * @param string[] $locales
     */
    #[DataProvider('getTestCases')]
    public function testConditionChecking(
        array $locales, 
        string $requestLocale,
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
            $requestLocale,
            [],
            new DateTimeImmutable(),
        );

        $routingStepClassRegistryMock = $this->createMock(RoutingStepClassRegistryInterface::class);
        $routingStepClassRegistryMock->expects($this->once())
                                ->method('getAliasForRoutingStepClass')
                                ->willReturn('');        

        $routingStepSchemeClassRegistry = $this->createMock(RoutingStepSchemeClassRegistryInterface::class);
        $routingStepSchemeClassRegistry->expects($this->once())
                                ->method('getRoutingStepSchemeClassByAlias')
                                ->willReturn(LocaleConditionScheme::class);

        $scheme = new LocaleConditionScheme();
        $scheme->locales = $locales;

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $denormalizerMock->expects($this->once())
                                ->method('denormalize')
                                ->willReturn($scheme);        

        $strategy = new LocaleCheckerStrategy(
            $routingStepClassRegistryMock,
            $routingStepSchemeClassRegistry,
            $denormalizerMock
        );

        $result = $strategy->doHandleRoutingStep($routingStepMock, $httpRequestDto, $this->createMock(RedirectionContextInterface::class));

        $this->assertEqualsCanonicalizing($nextRoutingStepMock, $result->getNextStep());        
    }

    /**
     * @return array<int, array{
     *  locales: string[],
     *  requestLocale: string,
     *  meetsCondition: bool
     * }>
     */
    public static function getTestCases(): array
    {
        return [
            [
                'locales' => ['en'],
                'requestLocale' => 'en',
                'meetsCondition' => true,
            ],                
            [
                'locales' => ['en','fr','de'],
                'requestLocale' => 'en',
                'meetsCondition' => true,
            ],         
            [
                'locales' => ['en'],
                'requestLocale' => 'fr',
                'meetsCondition' => false,
            ],         
            [
                'locales' => ['en', 'fr', 'de'],
                'requestLocale' => 'it',
                'meetsCondition' => false,
            ],                                             
        ];
    }
}