<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\RedirectUrl;

interface RedirectUrlRepositoryInterface
{
    public function findOneById(int $redirectUrlId): ?RedirectUrl;
}