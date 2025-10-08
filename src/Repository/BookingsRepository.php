<?php

namespace App\Repository;

use App\Entity\Bookings;
use App\Entity\CarSharings;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BookingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookings::class);
    }

    public function userAlreadyBookedTrip(User $user, CarSharings $trip): bool
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.carSharingId = :trip')
            ->andWhere('b.passenger = :user')
            ->andWhere('b.status IN (:st)')
            ->setParameter('trip', $trip)
            ->setParameter('user', $user)
            ->setParameter('st', ['CONFIRMED', 'PENDING'])
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function getParticipantsForTrip(CarSharings $trip): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.passenger', 'u')
            ->addSelect('u')
            ->andWhere('b.carSharingId = :trip')
            ->andWhere('b.status IN (:st)')
            ->setParameter('trip', $trip)
            ->setParameter('st', ['CONFIRMED'])
            ->orderBy('b.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countBookedSeats(CarSharings $trip): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COALESCE(SUM(b.seatsBooked), 0)')
            ->andWhere('b.carSharingId = :trip')
            ->andWhere('b.status IN (:st)')
            ->setParameter('trip', $trip)
            ->setParameter('st', ['CONFIRMED'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUserPassengerHistory(User $user, int $limit = 100): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.carSharingId', 'c')->addSelect('c')
            ->where('b.passenger = :u')
            ->setParameter('u', $user)
            ->orderBy('c.departureAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function findActiveBooking(User $user, CarSharings $trip): ?Bookings
    {
        return $this->createQueryBuilder('b')
            ->where('b.passenger = :u')
            ->andWhere('b.carSharingId = :t')
            ->andWhere('b.status IN (:ok)')
            ->setParameter('u', $user)
            ->setParameter('t', $trip)
            ->setParameter('ok', ['CONFIRMED'])
            ->getQuery()->getOneOrNullResult();
    }

    public function findTripConfirmedBookings(CarSharings $trip): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.carSharingId = :t')
            ->andWhere('b.status = :st')
            ->setParameter('t', $trip)
            ->setParameter('st', 'CONFIRMED')
            ->getQuery()->getResult();
    }
}
