<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\RouteInterface;

interface RouteRepositoryInterface
{
    public function findOneById(int $routeId): ?RouteInterface;

    public function save(RouteInterface $route): void;

    /**
     * @return RouteInterface[]
     */
    public function findAll(): array;

    /**
     * @return RouteInterface[]
     */
    public function findAllActiveDescByPriority(): array;

    public function doExistWithUrlPattern(string $urlPattern): bool;

    public function findOneByUrlPattern(string $urlPattern): ?RouteInterface;
}
