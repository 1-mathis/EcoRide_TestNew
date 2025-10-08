<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Reviews;
use App\Entity\TripReports;
use App\Entity\PassengerConfirmations;
use App\Repository\PassengerConfirmationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class PassengerConfirmationController extends AbstractController
{
    #[Route('/me/confirmations', name: 'app_me_confirmations', methods: ['GET'])]
    public function listForUser(PassengerConfirmationsRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('pc')
            ->join('pc.bookingId', 'b')
            ->join('b.carSharingId', 'cs')
            ->addSelect('b', 'cs')
            ->where('b.passenger = :u')
            ->setParameter('u', $user)
            ->orderBy('pc.createdAt', 'DESC');

        $items = $qb->getQuery()->getResult();

        return $this->render('user/confirmation.html.twig', [
            'confirmations' => $items,
        ]);
    }

    #[Route('/confirmations/{id}/confirm', name: 'app_confirmation_confirm', methods: ['POST'])]
    public function confirm(
        PassengerConfirmations $pc,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }
        if ($pc->getBookingId()?->getPassenger()?->getId() !== $user->getId()) {
            return $this->json(['ok' => false, 'message' => 'Non autorisé'], 403);
        }
        if (!$this->isCsrfTokenValid('confirm_pc_' . $pc->getId(), (string)$request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $pc->setStatus('CONFIRMED');
        $pc->setConfirmedAt(new \DateTime());
        $pc->setUpdatedAt(new \DateTime());

        $booking = $pc->getBookingId();
        $trip    = $booking?->getCarSharingId();
        $author  = $booking?->getPassenger();
        $driver  = $trip?->getDriverId(); 

        $rating  = (int) $request->request->get('rating', 0);
        if ($rating < 1 || $rating > 5) { $rating = 0; }
        $comment = trim((string) $request->request->get('comment', ''));

        $reviewCreated = false;

        if ($trip && $author && ($rating > 0 || $comment !== '')) {
            $existing = $em->getRepository(Reviews::class)->findOneBy([
                'authorId'      => $author,
                'carSharingsId' => $trip,
            ]);

            if (!$existing) {
                $review = new Reviews();
                if ($rating > 0) { $review->setRating($rating); }
                if ($comment !== '') { $review->setComment($comment); }
                $review->setStatus('pending');
                $review->setCreatedAt(new \DateTime());
                $review->setUpdatedAt(new \DateTime());
                $review->setAuthorId($author);
                if ($driver) { $review->setDriverId($driver); }
                $review->setCarSharingsId($trip);

                $em->persist($review);
                $reviewCreated = true;
            }
        }

        $em->persist($pc);
        $em->flush();

        $msg = 'Confirmation enregistrée' . ($reviewCreated ? ' + avis envoyé (en attente de validation).' : '.');

        return $this->json(['ok' => true, 'message' => $msg, 'reviewCreated' => $reviewCreated]);
    }

    #[Route('/confirmations/{id}/report', name: 'app_confirmation_report', methods: ['POST'])]
    public function report(
        PassengerConfirmations $pc,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }
        if ($pc->getBookingId()?->getPassenger()?->getId() !== $user->getId()) {
            return $this->json(['ok' => false, 'message' => 'Non autorisé'], 403);
        }
        if (!$this->isCsrfTokenValid('report_pc_' . $pc->getId(), (string)$request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $comment = trim((string)$request->request->get('comment', ''));
        if ($comment === '') {
            return $this->json(['ok' => false, 'message' => 'Merci de préciser un commentaire'], 400);
        }

        $pc->setStatus('REPORTED');
        $pc->setComment($comment);
        $pc->setUpdatedAt(new \DateTime());

        $booking  = $pc->getBookingId();
        $trip     = $booking?->getCarSharingId();
        $reporter = $booking?->getPassenger();
        $driver   = $trip?->getDriverId();

        $incident = new TripReports();
        $incident->setReason($comment);
        $incident->setStatus('open');
        $incident->setCreatedAt(new \DateTime());
        $incident->setUpdatedAt(new \DateTime());
        if ($trip)     { $incident->setCarSharingsId($trip); }
        if ($reporter) { $incident->setReporterId($reporter); }
        if ($driver)   { $incident->setDriverId($driver); }

        $em->persist($incident);
        $em->persist($pc);
        $em->flush();

        return $this->json(['ok' => true, 'message' => 'Signalement enregistré. Un employé va traiter votre demande.']);
    }
}
