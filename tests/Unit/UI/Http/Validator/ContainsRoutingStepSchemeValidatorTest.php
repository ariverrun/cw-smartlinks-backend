<?php

declare(strict_types=1);

namespace Tests\Unit\UI\Http\Validator;

use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\UI\Http\Validator\ContainsRoutingStepScheme;
use App\UI\Http\Validator\ContainsRoutingStepSchemeValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use stdClass;

final class ContainsRoutingStepSchemeValidatorTest extends TestCase
{
    public function testSupportsOnlyContainsRoutingStepSchemeConstraint(): void
    {
        $valueMock = $this->createMock(RoutingStepNestedDto::class);
        $constraintMock = $this->createMock(Constraint::class);

        $validator = new ContainsRoutingStepSchemeValidator(
            $this->createMock(RoutingStepSchemeClassRegistryInterface::class),
            $this->createMock(DenormalizerInterface::class),
            $this->createMock(ValidatorInterface::class),
        );

        $this->expectException(UnexpectedTypeException::class);

        $validator->validate($valueMock, $constraintMock);
    }

    public function testSupportsOnlyRoutingStepNestedDtoValue(): void
    {
        $valueMock = $this->createMock(stdClass::class);
        $constraintMock = $this->createMock(ContainsRoutingStepScheme::class);

        $validator = new ContainsRoutingStepSchemeValidator(
            $this->createMock(RoutingStepSchemeClassRegistryInterface::class),
            $this->createMock(DenormalizerInterface::class),
            $this->createMock(ValidatorInterface::class),
        );

        $this->expectException(UnexpectedValueException::class);

        $validator->validate($valueMock, $constraintMock);
    }
}