<?php

namespace App\Controller\Admin;

use App\Repository\CreditsLedgerRepository;
use Doctrine\DBAL\Types\Types as DBALTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $em, CreditsLedgerRepository $ledgerRepo): Response
    {
        $from = new \DateTime('today -29 days');
        $to   = new \DateTime('tomorrow');

        $total = (int) $ledgerRepo->createQueryBuilder('l')
            ->select('COALESCE(SUM(l.amount), 0)')
            ->andWhere('l.direction = :dir')
            ->andWhere('l.reason LIKE :reason')
            ->setParameter('dir', 'IN')
            ->setParameter('reason', 'platform%')
            ->getQuery()
            ->getSingleScalarResult();

        $conn = $em->getConnection();

        $rowsTrips = $conn->fetchAllAssociative(
            "SELECT DATE(departure_at) AS d, COUNT(id) AS n
             FROM car_sharings
             WHERE departure_at >= :from AND departure_at < :to
             GROUP BY d
             ORDER BY d ASC",
            ['from' => $from, 'to' => $to],
            ['from' => DBALTypes::DATETIME_MUTABLE, 'to' => DBALTypes::DATETIME_MUTABLE]
        );

        $rowsCreds = $conn->fetchAllAssociative(
            "SELECT DATE(created_at) AS d, COALESCE(SUM(amount), 0) AS n
             FROM credits_ledger
             WHERE created_at >= :from AND created_at < :to
               AND direction = :dir
               AND reason LIKE :reason
             GROUP BY d
             ORDER BY d ASC",
            [
                'from'   => $from,
                'to'     => $to,
                'dir'    => 'IN',
                'reason' => 'platform%',
            ],
            [
                'from'   => DBALTypes::DATETIME_MUTABLE,
                'to'     => DBALTypes::DATETIME_MUTABLE,
                'dir'    => \PDO::PARAM_STR,
                'reason' => \PDO::PARAM_STR,
            ]
        );

        [$labels, $trips]  = $this->makeDailySerie($from, $to, $rowsTrips, 'd', 'n');
        [,         $creds] = $this->makeDailySerie($from, $to, $rowsCreds, 'd', 'n');

        return $this->render('admin/dashboard.html.twig', [
            'kpi_total_platform_credits' => $total,
            'labels'  => $labels,
            'trips'   => $trips,
            'credits' => $creds,
        ]);
    }

    /**
     * @param array<int,array<string,mixed>> $rows (ex: ['d' => '2025-09-01', 'n' => 3])
     * @return array{0: string[], 1: int[]}
     */
    private function makeDailySerie(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        array $rows,
        string $dateKey = 'd',
        string $valueKey = 'n'
    ): array {
        $map = [];
        foreach ($rows as $r) {
            $k = $r[$dateKey] instanceof \DateTimeInterface ? $r[$dateKey]->format('Y-m-d') : (string) $r[$dateKey];
            if ($k !== '') {
                $map[$k] = (int) $r[$valueKey];
            }
        }

        $labels = [];
        $data   = [];

        $period = new \DatePeriod($from, new \DateInterval('P1D'), $to);
        foreach ($period as $day) {
            /** @var \DateTimeInterface $day */
            $k = $day->format('Y-m-d');
            $labels[] = $k;
            $data[]   = $map[$k] ?? 0;
        }

        return [$labels, $data];
    }
}
