<?php

declare(strict_types=1);

namespace App\UI\Http\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
final class ContainsRoutingStepScheme extends Constraint
{
    public string $message = 'Unknown scheme `{{ scheme_type }}` for type `{{ type }}` or unknown type.';
}
