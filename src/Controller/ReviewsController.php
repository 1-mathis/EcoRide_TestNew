<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewsController extends AbstractController
{
    #[Route('/chauffeurs/{id}/avis', name: 'app_driver_reviews', methods: ['GET'])]
    public function driverReviews(User $driver, ReviewsRepository $reviews): Response
    {
        $items = $reviews->findApprovedByDriver($driver, 100);

        return $this->render('reviews/driver_list.html.twig', [
            'driver' => $driver,
            'reviews' => $items,
        ]);
    }
}
