<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ValueResolver;

use App\Infrastructure\ValueResolver\RequestForRedirectValueResolver;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class RequestForRedirectValueResolverTest extends TestCase
{
    public function testRequestResolving(): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestPath = '/test';

        $requestMock->expects($this->once())
                                ->method('getPathInfo')
                                ->willReturn($requestPath);  
                
        $requestLocale = 'en';

        $requestMock->expects($this->once())
                                ->method('getLocale')
                                ->willReturn($requestLocale);  
        
        $requestHeaders = [
            'foo' => 'bar',
        ];

        $headersMock = $this->createMock(HeaderBag::class);
        $headersMock->expects($this->once())
                                ->method('all')
                                ->willReturn($requestHeaders); 
                                
        $requestMock->headers = $headersMock;

        $requestTime = microtime(true);

        $serverMock = $this->createMock(ServerBag::class);
        $serverMock->expects($this->once())
                                ->method('get')
                                ->with('REQUEST_TIME_FLOAT')
                                ->willReturn($requestTime);  
        
        $requestMock->server = $serverMock;

        $mockedArgument = new class() {};

        $argumentMetadataMock = $this->createMock(ArgumentMetadata::class);
        $argumentMetadataMock->expects($this->once())
                                ->method('getType')
                                ->willReturn($mockedArgument::class); 

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $denormalizerMock->expects($this->once())
                         ->method('denormalize')
                         ->willReturnCallback(function(array $collectedData, string $argType) use(
                                $mockedArgument,
                                $requestPath,
                                $requestLocale,
                                $requestTime,
                            ): object {
                                $this->assertArrayHasKey('requestPath', $collectedData);
                                $this->assertEquals($requestPath, $collectedData['requestPath']);
                                $this->assertArrayHasKey('locale', $collectedData);
                                $this->assertEquals($requestLocale, $collectedData['locale']);    
                                $this->assertArrayHasKey('requestTime', $collectedData);
                                $this->assertEquals(
                                DateTimeImmutable::createFromFormat('U.u', (string)$requestTime)
                                                    ->format(DateTimeInterface::ATOM),
                                $collectedData['requestTime']
                                );    

                                $this->assertEquals($mockedArgument::class, $argType);

                                return $mockedArgument;
                            });


        $violationListMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationListMock->expects($this->once())
                        ->method('count')
                        ->willReturn(0);

        $validatorMock = $this->createMock(ValidatorInterface::class);

        $validatorMock->expects($this->once())
                        ->method('validate')
                        ->with($mockedArgument)
                        ->willReturn($violationListMock);
        
        $resolvedArgs = (new RequestForRedirectValueResolver($denormalizerMock, $validatorMock))
                                ->resolve($requestMock, $argumentMetadataMock);

        $this->assertArrayHasKey(0, $resolvedArgs);
        $this->assertEqualsCanonicalizing($mockedArgument, $resolvedArgs[0]);
    }

    public function testExceptionOnValidationRulesViolation(): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestPath = '/test';

        $requestMock->expects($this->once())
                                ->method('getPathInfo')
                                ->willReturn($requestPath);  
                
        $requestLocale = 'en';

        $requestMock->expects($this->once())
                                ->method('getLocale')
                                ->willReturn($requestLocale);  
        
        $requestHeaders = [
            'foo' => 'bar',
        ];

        $headersMock = $this->createMock(HeaderBag::class);
        $headersMock->expects($this->once())
                                ->method('all')
                                ->willReturn($requestHeaders); 
                                
        $requestMock->headers = $headersMock;

        $requestTime = microtime(true);

        $serverMock = $this->createMock(ServerBag::class);
        $serverMock->expects($this->once())
                                ->method('get')
                                ->with('REQUEST_TIME_FLOAT')
                                ->willReturn($requestTime);  
        
        $requestMock->server = $serverMock;

        $mockedArgument = new class() {};

        $argumentMetadataMock = $this->createMock(ArgumentMetadata::class);
        $argumentMetadataMock->expects($this->once())
                                ->method('getType')
                                ->willReturn($mockedArgument::class); 

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $denormalizerMock->expects($this->once())
                         ->method('denormalize')
                         ->willReturnCallback(function(array $collectedData, string $argType) use(
                                $mockedArgument,
                                $requestPath,
                                $requestLocale,
                                $requestTime,
                            ): object {
                                $this->assertArrayHasKey('requestPath', $collectedData);
                                $this->assertEquals($requestPath, $collectedData['requestPath']);
                                $this->assertArrayHasKey('locale', $collectedData);
                                $this->assertEquals($requestLocale, $collectedData['locale']);    
                                $this->assertArrayHasKey('requestTime', $collectedData);
                                $this->assertEquals(
                                DateTimeImmutable::createFromFormat('U.u', (string)$requestTime)
                                                    ->format(DateTimeInterface::ATOM),
                                $collectedData['requestTime']
                                );    

                                $this->assertEquals($mockedArgument::class, $argType);

                                return $mockedArgument;
                            });

        $violationListMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationListMock->expects($this->once())
                        ->method('count')
                        ->willReturn(1);

        $validatorMock = $this->createMock(ValidatorInterface::class);

        $validatorMock->expects($this->once())
                        ->method('validate')
                        ->with($mockedArgument)
                        ->willReturn($violationListMock);
        
        $this->expectException(InvalidArgumentException::class);

        (new RequestForRedirectValueResolver($denormalizerMock, $validatorMock))
                                ->resolve($requestMock, $argumentMetadataMock);
    }    
}