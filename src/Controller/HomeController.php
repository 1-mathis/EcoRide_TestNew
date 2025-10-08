<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        Request $request,
        #[Autowire(param: 'app.contact_email')] string $contactEmail = 'contact@ecoride.fr'
    ): Response {
        $from = trim((string) $request->query->get('from', ''));
        $to   = trim((string) $request->query->get('to', ''));
        $date = (string) $request->query->get('date', '');

        if ($from !== '' && $to !== '') {
            return $this->redirectToRoute('app_search', [
                'from' => $from,
                'to'   => $to,
                'date' => $date,
            ]);
        }

        return $this->render('home/home.html.twig', [
            'contact_email' => $contactEmail,
            'hero_images' => [
                'images/home/shot-1.jpg',
                'images/home/shot-2.jpg',
                'images/home/shot-3.jpg',
            ],
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function mentions(): Response
    {
        return $this->render('legal/mentions.html.twig');
    }
}
