<?php

namespace App\Controller;

use App\Repository\CarSharingsRepository;
use App\Repository\BookingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class HistoryController extends AbstractController
{
    #[Route('/historique', name: 'app_history_index', methods: ['GET'])]
    public function index(
        CarSharingsRepository $csRepo,
        BookingsRepository $bkRepo
    ): Response {
        $user = $this->getUser();

        $asDriver   = $csRepo->historyForDriver($user);
        $asPassenger = $bkRepo->findUserPassengerHistory($user);

        return $this->render('carSharings/history.html.twig', [
            'asDriver' => $asDriver,
            'asPassenger' => $asPassenger,
        ]);
    }
}
