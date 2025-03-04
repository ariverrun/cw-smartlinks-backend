<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Route;
use App\Domain\Entity\RouteInterface;
use App\Domain\Repository\RouteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Route>
 */
final class RouteRepository extends ServiceEntityRepository implements RouteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function findOneById(int $routeId): ?RouteInterface
    {
        $route = $this->find($routeId);

        return $route;
    }

    public function save(RouteInterface $route): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($route);
        $entityManager->flush();
    }

    public function findAllActiveDescByPriority(): array
    {
        return $this->findBy(['isActive' => true], ['priority' => 'DESC']);
    }

    public function doExistWithUrlPattern(string $urlPattern): bool
    {
        return (bool)$this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.urlPattern = :urlPattern')
            ->setParameter('urlPattern', $urlPattern)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
