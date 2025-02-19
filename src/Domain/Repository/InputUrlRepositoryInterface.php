<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\InputUrl;

interface InputUrlRepositoryInterface
{
    public function findOneById(int $inputUrlId): ?InputUrl;
}