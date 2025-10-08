<?php

namespace App\Repository;

use App\Entity\CarSharings;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CarSharingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarSharings::class);
    }

    /**
     *
     * @param array{
     *   eco?:bool,
     *   maxPrice?:float|null,
     *   maxDur?:int|null,
     *   minRating?:int|null
     * } $filtres
     * @return CarSharings[]
     */
    public function searchByCityDateWithFilters(
        string $from,
        string $to,
        \DateTimeInterface $date,
        array $filtres = [],
        int $limite = 50
    ): array {
        $start = new \DateTime($date->format('Y-m-d') . ' 00:00:00');
        $end   = (clone $start)->modify('+1 day');

        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.driverId', 'd')->addSelect('d')
            ->leftJoin('c.vehicleId', 'v')->addSelect('v')
            ->where('LOWER(c.fromCity) LIKE LOWER(:from)')
            ->andWhere('LOWER(c.toCity) LIKE LOWER(:to)')
            ->andWhere('c.departureAt >= :start AND c.departureAt < :end')
            ->andWhere('c.seatsRemaining > 0')
            ->setParameter('from', '%' . trim($from) . '%')
            ->setParameter('to', '%' . trim($to) . '%')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('c.departureAt', 'ASC')
            ->setMaxResults($limite);

        if (!empty($filtres['eco'])) {
            $qb->andWhere('c.isEco = 1');
        }
        if (array_key_exists('maxPrice', $filtres) && $filtres['maxPrice'] !== null) {
            $qb->andWhere('c.price <= :maxPrice')
               ->setParameter('maxPrice', (float) $filtres['maxPrice']);
        }
        if (array_key_exists('maxDur', $filtres) && $filtres['maxDur'] !== null) {
            $qb->andWhere('c.durationMinutes <= :maxDur')
               ->setParameter('maxDur', (int) $filtres['maxDur']);
        }
        if (array_key_exists('minRating', $filtres) && $filtres['minRating'] !== null) {
            $qb->andWhere('d.avgRating >= :minRating')
               ->setParameter('minRating', (int) $filtres['minRating']);
        }

        return $qb->getQuery()->getResult();
    }

    public function suggestNextDateWithAvailability(
        string $from,
        string $to,
        \DateTimeInterface $date,
        array $filtres = []
    ): ?\DateTimeInterface {
        $start = new \DateTime($date->format('Y-m-d') . ' 00:00:00');

        $qb = $this->createQueryBuilder('c')
            ->select('c.departureAt')
            ->leftJoin('c.driverId', 'd')
            ->leftJoin('c.vehicleId', 'v')
            ->where('LOWER(c.fromCity) LIKE LOWER(:from)')
            ->andWhere('LOWER(c.toCity) LIKE LOWER(:to)')
            ->andWhere('c.departureAt >= :start')
            ->andWhere('c.seatsRemaining > 0')
            ->setParameter('from', '%' . trim($from) . '%')
            ->setParameter('to', '%' . trim($to) . '%')
            ->setParameter('start', $start)
            ->orderBy('c.departureAt', 'ASC')
            ->setMaxResults(1);

        if (!empty($filtres['eco'])) {
            $qb->andWhere('c.isEco = 1');
        }
        if (array_key_exists('maxPrice', $filtres) && $filtres['maxPrice'] !== null) {
            $qb->andWhere('c.price <= :maxPrice')
               ->setParameter('maxPrice', (float) $filtres['maxPrice']);
        }
        if (array_key_exists('maxDur', $filtres) && $filtres['maxDur'] !== null) {
            $qb->andWhere('c.durationMinutes <= :maxDur')
               ->setParameter('maxDur', (int) $filtres['maxDur']);
        }
        if (array_key_exists('minRating', $filtres) && $filtres['minRating'] !== null) {
            $qb->andWhere('d.avgRating >= :minRating')
               ->setParameter('minRating', (int) $filtres['minRating']);
        }

        /** @var array{departureAt:\DateTimeInterface}|null $row */
        $row = $qb->getQuery()->getOneOrNullResult();

        return $row['departureAt'] ?? null;
    }

    public function historyForDriver(User $user, int $limit = 100): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.driverId = :u')
            ->setParameter('u', $user)
            ->orderBy('c.departureAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}
