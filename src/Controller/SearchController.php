<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarSharingsRepository;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(Request $request, CarSharingsRepository $carSharingsRepo): Response
    {
        $from = trim((string) $request->query->get('from', ''));
        $to   = trim((string) $request->query->get('to', ''));
        $date = (string) $request->query->get('date', '');
        $eco = (bool) $request->query->get('eco', false);
        $maxPriceRaw    = $request->query->get('maxPrice', null);
        $maxDurationRaw = $request->query->get('maxDuration', null);
        $minRatingRaw   = $request->query->get('minRating', null);

        $maxPrice    = ($maxPriceRaw === null || $maxPriceRaw === '') ? null : (float) $maxPriceRaw;
        $maxDuration = ($maxDurationRaw === null || $maxDurationRaw === '') ? null : (int) $maxDurationRaw;
        $minRating   = ($minRatingRaw === null || $minRatingRaw === '') ? null : (int) $minRatingRaw;

        /** @var DateTimeInterface|null $queryDate */
        $queryDate = null;
        if ($date !== '') {
            $queryDate = DateTime::createFromFormat('Y-m-d', $date) ?: null;
        }

        $results    = [];
        $suggestion = null;

        if ($from !== '' && $to !== '' && $queryDate instanceof DateTimeInterface) {
            $filters = [
                'eco'       => $eco,
                'maxPrice'  => $maxPrice,
                'maxDur'    => $maxDuration,
                'minRating' => $minRating,
            ];

            $results = $carSharingsRepo->searchByCityDateWithFilters(
                $from,
                $to,
                $queryDate,
                $filters,
                100
            );

            if (!$results) {
                $suggestion = $carSharingsRepo->suggestNextDateWithAvailability(
                    $from,
                    $to,
                    $queryDate,
                    $filters
                );
            }
        }

        return $this->render('search/search.html.twig', [
            'from'        => $from,
            'to'          => $to,
            'date'        => $date,
            'results'     => $results,
            'suggestion'  => $suggestion,
            'eco'         => $eco,
            'maxPrice'    => $maxPriceRaw,
            'maxDuration' => $maxDurationRaw,
            'minRating'   => $minRatingRaw,
        ]);
    }
}
