<?php

namespace App\Controller\employee;

use App\Repository\ReviewsRepository;
use App\Repository\TripReportsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employe')]
class EmployeeDashboardController extends AbstractController
{
    #[Route('', name: 'employee_dashboard')]
    public function index(ReviewsRepository $reviews, TripReportsRepository $reports): Response
    {
        $pendingReviews = $reviews->countByStatus('pending');
        $openIncidents  = $reports->countByStatus('open');

        return $this->render('employee/dashboard.html.twig', [
            'pendingReviews' => $pendingReviews,
            'openIncidents'  => $openIncidents,
        ]);
    }
}
