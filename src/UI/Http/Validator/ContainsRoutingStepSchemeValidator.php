<?php

declare(strict_types=1);

namespace App\UI\Http\Validator;

use App\Application\Dto\RoutingStepNestedDto;
use App\Application\Exception\UnknownRoutingStepSchemeException;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Registry\RoutingStepSchemeClassRegistryInterface;
use App\UI\Http\Controller\Api\RoutingStepRequestNestedDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContainsRoutingStepSchemeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RoutingStepSchemeClassRegistryInterface $routingStepSchemeClassRegistry,
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContainsRoutingStepScheme) {
            throw new UnexpectedTypeException($constraint, ContainsRoutingStepScheme::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof RoutingStepRequestNestedDto) {
            throw new UnexpectedValueException($value, RoutingStepNestedDto::class);
        }

        $schemeClass = null;

        try {

            $schemeClass = $this->routingStepSchemeClassRegistry->getRoutingStepSchemeClassByAlias(
                $value->type . '.' . $value->schemeType
            );

        } catch (UnknownRoutingStepSchemeException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ scheme_type }}', $value->schemeType)
                ->setParameter('{{ type }}', $value->type)
                ->addViolation();

            return;
        }

        /** @var class-string<RoutingStepSchemeInterface> $schemeClass */
        $routingStepScheme = $this->denormalizer->denormalize($value->schemeProps, $schemeClass);

        $violationsList = $this->validator->validate($routingStepScheme);

        foreach ($violationsList as $violation) {
            $this->context->addViolation(
                $violation->getMessage(),
                $violation->getParameters(),
            );
        }
    }
}
