<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Route;
use App\Domain\Repository\RouteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RouteRepository extends ServiceEntityRepository implements RouteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function findOneById(int $routeId): ?Route
    {
        $route = $this->find($routeId);

        return $route;
    }

    public function save(Route $route): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($route);
        $entityManager->flush();
    }

    public function findAllActiveDescByPriority(): array
    {
        return $this->findBy(['isActive' => true], ['priority' => 'Desc']);
    }
}
