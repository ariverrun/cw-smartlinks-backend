<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\InputUrl;
use App\Domain\Repository\InputUrlRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class InputUrlRepository extends ServiceEntityRepository implements InputUrlRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputUrl::class);
    }

    public function findOneById(int $inputUrlId): ?InputUrl
    {
        /**
         * @var InputUrl|null $inputUrl
         */
        $inputUrl = $this->find($inputUrlId);

        return $inputUrl;
    }
}