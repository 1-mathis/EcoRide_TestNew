<?php

namespace App\Controller\employee;

use App\Entity\Reviews;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employe/avis')]
class EmployeeReviewController extends AbstractController
{
    #[Route('', name: 'employee_reviews')]
    public function list(Request $req, ReviewsRepository $repo): Response
    {
        $status = $req->query->get('status');
        if (!in_array($status, ['pending','approved','rejected'], true)) {
            $status = 'pending';
        }

        $items = $repo->findByStatus($status, 100);

        return $this->render('employee/reviews.html.twig', [
            'items'  => $items,
            'status' => $status,
        ]);
    }

    #[Route('/{id}/approve', name: 'employee_reviews_approve', methods: ['POST'])]
    public function approve(Reviews $review, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        if (!$this->isCsrfTokenValid('rev_approve_'.$review->getId(), $req->request->get('_token') ?? '')) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $review->setStatus('approved');
        $review->setReviewedAt(new \DateTime());
        $review->setReviewedBy($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Avis approuvé.');
        return $this->redirectToRoute('employee_reviews', ['status' => 'pending']);
    }

    #[Route('/{id}/reject', name: 'employee_reviews_reject', methods: ['POST'])]
    public function reject(Reviews $review, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        if (!$this->isCsrfTokenValid('rev_reject_'.$review->getId(), $req->request->get('_token') ?? '')) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $review->setStatus('rejected');
        $review->setReviewedAt(new \DateTime());
        $review->setReviewedBy($this->getUser());

        $em->flush();

        $this->addFlash('success', 'Avis refusé.');
        return $this->redirectToRoute('employee_reviews', ['status' => 'pending']);
    }
}
