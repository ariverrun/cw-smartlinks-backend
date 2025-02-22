<?php

declare(strict_types=1);

namespace App\Application\Enum;

enum UrlPatternPartType: int
{
    case STRICT_PART = 0;
    case ANY_PARAMETER = 1;
    case ACCEPT_ALL = 2;
}
