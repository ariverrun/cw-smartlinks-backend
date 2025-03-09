<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mapper;

use App\Application\Exception\InvalidMappingResultException;
use App\Application\Service\Mapper\AutoMapperInterface;
use App\Infrastructure\Mapper\ValidatingAutoMapperDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use stdClass;

final class ValidatingAutoMapperDecoratorTest extends TestCase
{
    public function testThrowsExceptionOnValidationRulesViolation(): void
    {
        $autoMapperMock = $this->createMock(AutoMapperInterface::class);

        $autoMapperMock->expects($this->once())
                        ->method('map')
                        ->willReturn($this->createMock(stdClass::class));

        $validatorMock = $this->createMock(ValidatorInterface::class);

        $violationListMock = $this->createMock(ConstraintViolationListInterface::class);

        $violationListMock->expects($this->once())
                        ->method('count')
                        ->willReturn(1);

        $validatorMock->expects($this->once())
                        ->method('validate')
                        ->willReturn($violationListMock);        
                    
        $this->expectException(InvalidMappingResultException::class);
        
        $validatingAutoMapper = new ValidatingAutoMapperDecorator($autoMapperMock, $validatorMock);

        $validatingAutoMapper->map($this->createMock(stdClass::class), stdClass::class);
    }
}