<?php

namespace App\Controller\employee;

use App\Entity\Reviews;
use App\Entity\TripReports;
use App\Entity\User;
use App\Repository\ReviewsRepository;
use App\Repository\TripReportsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[IsGranted('ROLE_EMPLOYE')]
#[Route('/employe/api')]
final class EmployeeApiController extends AbstractController
{
    #[Route('/reviews', name: 'employee_api_reviews', methods: ['GET'])]
    public function listReviews(Request $request, ReviewsRepository $repo, CsrfTokenManagerInterface $csrf): JsonResponse
    {
        $status = $request->query->get('status', 'pending');
        if (!in_array($status, ['pending','approved','rejected'], true)) {
            $status = 'pending';
        }

        $limit  = max(1, (int) $request->query->get('limit', 50));
        $offset = max(0, (int) $request->query->get('offset', 0));

        $items = $repo->findByStatusPaginated($status, $limit, $offset);
        $total = $repo->countByStatus($status);

        $rows = [];
        foreach ($items as $r) {
            /** @var Reviews $r */
            $trip   = $r->getCarSharingsId();
            $author = $r->getAuthorId();
            $driver = $r->getDriverId();

            $rows[] = [
                'id'        => $r->getId(),
                'rating'    => $r->getRating(),
                'comment'   => $r->getComment(),
                'status'    => $r->getStatus(),
                'createdAt' => $r->getCreatedAt()?->format('Y-m-d H:i'),
                'trip'      => $trip ? [
                    'id'          => $trip->getId(),
                    'fromCity'    => $trip->getFromCity(),
                    'toCity'      => $trip->getToCity(),
                    'departureAt' => $trip->getDepartureAt()?->format('Y-m-d H:i'),
                    'arrivalAt'   => $trip->getArrivalAt()?->format('Y-m-d H:i'),
                ] : null,
                'author' => $author ? [
                    'id' => $author->getId(),
                    'username' => $author->getUsername(),
                    'email' => $author->getEmail(),
                ] : null,
                'driver' => $driver ? [
                    'id' => $driver->getId(),
                    'username' => $driver->getUsername(),
                    'email' => $driver->getEmail(),
                ] : null,
                'tokens' => [
                    'approve' => (string) $csrf->getToken('rev_approve_' . $r->getId()),
                    'reject'  => (string) $csrf->getToken('rev_reject_'  . $r->getId()),
                ],
            ];
        }

        return $this->json(['ok' => true, 'total' => $total, 'data' => $rows]);
    }

    #[Route('/reports', name: 'employee_api_reports', methods: ['GET'])]
    public function listReports(Request $request, TripReportsRepository $repo, CsrfTokenManagerInterface $csrf): JsonResponse
    {
        $status = $request->query->get('status', 'open');
        if (!in_array($status, ['open','resolved','rejected'], true)) {
            $status = 'open';
        }

        $limit  = max(1, (int) $request->query->get('limit', 50));
        $offset = max(0, (int) $request->query->get('offset', 0));

        $items = $repo->findByStatusPaginated($status, $limit, $offset);
        $total = $repo->countByStatus($status);

        $rows = [];
        foreach ($items as $tr) {
            /** @var TripReports $tr */
            $trip     = $tr->getCarSharingsId();
            $reporter = $tr->getReporterId();
            $driver   = $tr->getDriverId();

            $rows[] = [
                'id'        => $tr->getId(),
                'reason'    => $tr->getReason(),
                'status'    => $tr->getStatus(),
                'createdAt' => $tr->getCreatedAt()?->format('Y-m-d H:i'),
                'trip'      => $trip ? [
                    'id'          => $trip->getId(),
                    'fromCity'    => $trip->getFromCity(),
                    'toCity'      => $trip->getToCity(),
                    'departureAt' => $trip->getDepartureAt()?->format('Y-m-d H:i'),
                    'arrivalAt'   => $trip->getArrivalAt()?->format('Y-m-d H:i'),
                ] : null,
                'reporter' => $reporter ? [
                    'id' => $reporter->getId(),
                    'username' => $reporter->getUsername(),
                    'email' => $reporter->getEmail(),
                ] : null,
                'driver' => $driver ? [
                    'id' => $driver->getId(),
                    'username' => $driver->getUsername(),
                    'email' => $driver->getEmail(),
                ] : null,
                'tokens' => [
                    'resolve' => (string) $csrf->getToken('rep_resolve_' . $tr->getId()),
                    'reject'  => (string) $csrf->getToken('rep_reject_'  . $tr->getId()),
                ],
            ];
        }

        return $this->json(['ok' => true, 'total' => $total, 'data' => $rows]);
    }

    #[Route('/reviews/{id}/approve', name: 'employee_api_review_approve', methods: ['POST'])]
    public function approveReview(Reviews $review, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('rev_approve_' . $review->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        /** @var User $employee */
        $employee = $this->getUser();
        $review->setStatus('approved');
        $review->setReviewedAt(new \DateTime());
        $review->setReviewedBy($employee);
        $review->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['ok' => true, 'message' => 'Avis approuvé.']);
    }

    #[Route('/reviews/{id}/reject', name: 'employee_api_review_reject', methods: ['POST'])]
    public function rejectReview(Reviews $review, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('rev_reject_' . $review->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        /** @var User $employee */
        $employee = $this->getUser();
        $review->setStatus('rejected');
        $review->setReviewedAt(new \DateTime());
        $review->setReviewedBy($employee);
        $review->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['ok' => true, 'message' => 'Avis refusé.']);
    }

    #[Route('/reports/{id}/resolve', name: 'employee_api_report_resolve', methods: ['POST'])]
    public function resolveReport(TripReports $report, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('rep_resolve_' . $report->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        /** @var User $employee */
        $employee = $this->getUser();
        $report->setStatus('resolved');
        $report->setHandleAt(new \DateTime());
        $report->setHandledBy($employee);
        $report->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['ok' => true, 'message' => 'Incident résolu.']);
    }

    #[Route('/reports/{id}/reject', name: 'employee_api_report_reject', methods: ['POST'])]
    public function rejectReport(TripReports $report, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('rep_reject_' . $report->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        /** @var User $employee */
        $employee = $this->getUser();
        $report->setStatus('rejected');
        $report->setHandleAt(new \DateTime());
        $report->setHandledBy($employee);
        $report->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['ok' => true, 'message' => 'Incident rejeté.']);
    }
}
