<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Route;

interface RouteRepositoryInterface
{
    public function findOneById(int $routeId): ?Route;

    public function save(Route $route): void;

    /**
     * @return Route[]
     */
    public function findAll(): array;

    /**
     * @return Route[]
     */
    public function findAllActiveDescByPriority(): array;
}
