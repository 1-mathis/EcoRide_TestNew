<?php

namespace App\Repository;

use App\Entity\TripReports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripReports>
 */
class TripReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripReports::class);
    }

    public function findByStatus(string $status, int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :s')->setParameter('s', $status)
            ->leftJoin('t.carSharingsId', 'cs')->addSelect('cs')
            ->leftJoin('t.reporterId', 'rp')->addSelect('rp')
            ->leftJoin('t.driverId',   'dr')->addSelect('dr')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function findByStatusPaginated(string $status, int $limit = 50, int $offset = 0): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :s')->setParameter('s', $status)
            ->leftJoin('t.carSharingsId', 'cs')->addSelect('cs')
            ->leftJoin('t.reporterId', 'rp')->addSelect('rp')
            ->leftJoin('t.driverId',   'dr')->addSelect('dr')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->setFirstResult(max(0, $offset))
            ->getQuery()->getResult();
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.status = :s')->setParameter('s', $status)
            ->getQuery()->getSingleScalarResult();
    }
}
