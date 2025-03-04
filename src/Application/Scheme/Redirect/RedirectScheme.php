<?php

declare(strict_types=1);

namespace App\Application\Scheme\Redirect;

use App\Application\Scheme\RoutingStepSchemeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RedirectScheme implements RoutingStepSchemeInterface
{
    #[Assert\NotBlank]
    public string $url;
}
