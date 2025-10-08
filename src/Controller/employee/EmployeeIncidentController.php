<?php

namespace App\Controller\employee;

use App\Entity\TripReports;
use App\Repository\TripReportsRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employe/incidents')]
class EmployeeIncidentController extends AbstractController
{
    #[Route('', name: 'employee_incidents')]
    public function list(Request $req, TripReportsRepository $repo): Response
    {
        $status = $req->query->get('status');
        if (!in_array($status, ['open','resolved','rejected'], true)) {
            $status = 'open';
        }

        $items = $repo->findByStatus($status, 100);

        return $this->render('employee/incidents.html.twig', [
            'items'  => $items,
            'status' => $status,
        ]);
    }

    #[Route('/{id}/resolve', name: 'employee_incidents_resolve', methods: ['POST'])]
    public function resolve(TripReports $report, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        if (!$this->isCsrfTokenValid('rep_resolve_'.$report->getId(), $req->request->get('_token') ?? '')) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $report->setStatus('resolved');
        $report->setHandleAt(new \DateTime());
        $report->setHandledBy($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Incident marqué comme résolu.');
        return $this->redirectToRoute('employee_incidents', ['status' => 'open']);
    }

    #[Route('/{id}/reject', name: 'employee_incidents_reject', methods: ['POST'])]
    public function reject(TripReports $report, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        if (!$this->isCsrfTokenValid('rep_reject_'.$report->getId(), $req->request->get('_token') ?? '')) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $report->setStatus('rejected');
        $report->setHandleAt(new \DateTime());
        $report->setHandledBy($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Incident rejeté.');
        return $this->redirectToRoute('employee_incidents', ['status' => 'open']);
    }
}
