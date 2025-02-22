<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\InputUrl;

interface InputUrlRepositoryInterface
{
    public function findOneById(int $inputUrlId): ?InputUrl;

    public function save(InputUrl $inputUrl): void;

    /**
     * @return InputUrl[]
     */
    public function findAll(): array;

    /**
     * @return InputUrl[]
     */
    public function findAllActiveDescByPriority(): array;
}
