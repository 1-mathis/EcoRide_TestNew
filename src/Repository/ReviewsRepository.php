<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Reviews;
use App\Entity\CarSharings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reviews::class);
    }

    public function findByDriver(User $driver, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.driverId = :driver')
            ->setParameter('driver', $driver)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getDriverStats(User $driver): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as avg, COUNT(r.id) as count')
            ->andWhere('r.driverId = :driver')
            ->setParameter('driver', $driver);

        $row = $qb->getQuery()->getSingleResult();

        return [
            'avg'   => $row['avg'] ? round((float) $row['avg'], 1) : null,
            'count' => (int) $row['count'],
        ];
    }

    public function findByCarSharing(CarSharings $cs, int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.carSharingsId = :cs')
            ->setParameter('cs', $cs)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findByStatus(string $status, int $limit = 50): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :s')->setParameter('s', $status)
            ->leftJoin('r.carSharingsId', 'cs')->addSelect('cs')
            ->leftJoin('r.authorId', 'au')->addSelect('au')
            ->leftJoin('r.driverId', 'dr')->addSelect('dr')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function findByStatusPaginated(string $status, int $limit = 50, int $offset = 0): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :s')->setParameter('s', $status)
            ->leftJoin('r.carSharingsId', 'cs')->addSelect('cs')
            ->leftJoin('r.authorId', 'au')->addSelect('au')
            ->leftJoin('r.driverId', 'dr')->addSelect('dr')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->setFirstResult(max(0, $offset))
            ->getQuery()->getResult();
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.status = :s')->setParameter('s', $status)
            ->getQuery()->getSingleScalarResult();
    }

    public function findApprovedByDriver(User $driver, int $limit = 20): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.driverId = :driver')->setParameter('driver', $driver)
            ->andWhere('r.status = :s')->setParameter('s', 'approved')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function findApprovedByCarSharing(CarSharings $cs, int $limit = 20): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.carSharingsId = :cs')->setParameter('cs', $cs)
            ->andWhere('r.status = :s')->setParameter('s', 'approved')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}
