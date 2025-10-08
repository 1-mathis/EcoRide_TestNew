<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\VehicleEnergies;
use App\Entity\Bookings;
use App\Form\CarSharingType;
use App\Repository\ReviewsRepository;
use App\Repository\BookingsRepository;
use App\Repository\PassengerConfirmationsRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class CarSharingController extends AbstractController
{
    #[Route('/covoiturages/{id}', name: 'app_carpool_show', methods: ['GET'])]
    public function show(
        CarSharings $trip,
        ReviewsRepository $reviewRepo,
        Request $request,
        BookingsRepository $bookingsRepo
    ): Response {
        $driver = $trip->getDriverId();
        $vehicle = $trip->getVehicleId();

        $reviews = $reviewRepo->findByDriver($driver, 10);
        $stats = $reviewRepo->getDriverStats($driver);

        $eco = $trip->isEco() ?? false;
        $energyLabels = [];

        if ($vehicle) {
            $energy = $vehicle->getEnergy();
            if ($energy instanceof VehicleEnergies) {
                $label = $energy->getLabel() ?? $energy->getCode();
                if ($label) {
                    $energyLabels[] = $label;
                }
                if (method_exists($energy, 'isElectric') && $energy->isElectric()) {
                    $eco = true;
                }
            }
        }

        $driverPreferences = [];
        if ($driver && method_exists($driver, 'getDriverPreferences')) {
            foreach ($driver->getDriverPreferences() as $pref) {
                $key = $pref->getKeyName() ?? '';
                $value = $pref->getValueText() ?? '';
                $prettyKey = $key !== '' ? ucfirst(strtolower(str_replace('_', ' ', (string) $key))) : '';
                $prettyValue = match (strtolower((string) $value)) {
                    '1', 'true', 'yes', 'oui' => 'Oui',
                    '0', 'false', 'no', 'non' => 'Non',
                    default => (string) $value,
                };
                $display = trim($prettyKey) !== '' ? trim($prettyKey . ' : ' . $prettyValue) : $prettyValue;
                if ($display !== '') {
                    $driverPreferences[] = $display;
                }
            }
        }

        $openConfirm = (bool) $request->query->get('confirm', false);
        $alreadyJoined = false;
        $current = $this->getUser();
        if ($current instanceof User) {
            $alreadyJoined = $bookingsRepo->userAlreadyBookedTrip($current, $trip);
        }

        $participants = $bookingsRepo->getParticipantsForTrip($trip);

        return $this->render('carSharings/show.html.twig', [
            'trip' => $trip,
            'driver' => $driver,
            'vehicle' => $vehicle,
            'reviews' => $reviews,
            'avgRating' => $stats['avg'] ?? null,
            'reviewsCount' => $stats['count'] ?? 0,
            'eco' => $eco,
            'energyLabels' => $energyLabels,
            'driverPreferences' => $driverPreferences,
            'openConfirm' => $openConfirm,
            'alreadyJoined' => $alreadyJoined,
            'participants' => $participants,
        ]);
    }

    #[Route('/covoiturages/{id}/participer', name: 'app_carpool_join', methods: ['GET'])]
    public function join(CarSharings $trip): Response
    {
        $current = $this->getUser();
        if ($current instanceof User && $trip->getDriverId()?->getId() === $current->getId()) {
            $this->addFlash('info', 'C’est ton trajet : pas besoin de réserver 😄');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }

        return $this->redirectToRoute('app_carpool_show', [
            'id' => $trip->getId(),
            'confirm' => 1,
        ]);
    }

    #[Route('/covoiturages/{id}/participer/confirmer', name: 'app_carpool_join_confirm', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function joinConfirm(
        Request $request,
        CarSharings $trip,
        BookingsRepository $bookingsRepo,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): RedirectResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('error', 'Tu dois être connecté pour réserver.');
            return $this->redirectToRoute('app_login');
        }

        $token = (string) $request->request->get('_token', '');
        if ($token === '') {
            $this->addFlash('error', 'Jeton CSRF manquant.');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }
        if (!$this->isCsrfTokenValid('join_trip_' . $trip->getId(), $token)) {
            $this->addFlash('error', 'Jeton CSRF invalide. Merci de ré-essayer.');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }

        if (($trip->getSeatsRemaining() ?? 0) <= 0) {
            $this->addFlash('error', 'Plus de place disponible.');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }
        if ($trip->getDriverId() && $trip->getDriverId()->getId() === $user->getId()) {
            $this->addFlash('error', 'Tu ne peux pas réserver ton propre trajet 🤨');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }
        if ($bookingsRepo->userAlreadyBookedTrip($user, $trip)) {
            $this->addFlash('info', 'Tu as déjà une réservation sur ce trajet.');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }

        $costCredits = (int) ceil((float) $trip->getPrice());
        if (($user->getCredit() ?? 0) < $costCredits) {
            $this->addFlash('error', 'Crédit insuffisant pour réserver.');
            return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
        }

        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $em->lock($trip, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($trip);

            $seatsLeft = (int) ($trip->getSeatsRemaining() ?? 0);
            if ($seatsLeft <= 0) {
                throw new \RuntimeException('Plus de place disponible (re-check).');
            }
            if ($bookingsRepo->userAlreadyBookedTrip($user, $trip)) {
                throw new \RuntimeException('Réservation déjà existante (re-check).');
            }
            $currentCredits = (int) ($user->getCredit() ?? 0);
            if ($currentCredits < $costCredits) {
                throw new \RuntimeException('Crédit insuffisant (re-check).');
            }

            $trip->setSeatsRemaining($seatsLeft - 1);
            $user->setCredit($currentCredits - $costCredits);

            $now = new \DateTime();
            $booking = (new Bookings())
                ->setCarSharingId($trip)
                ->setPassenger($user)
                ->setSeatsBooked(1)
                ->setStatus('CONFIRMED')
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            $em->persist($trip);
            $em->persist($user);
            $em->persist($booking);
            $em->flush();

            $conn->commit();
            $this->addFlash('success', 'Réservation confirmée. Bon voyage !');
        } catch (\Throwable $e) {
            $conn->rollBack();
            $logger->error('Join confirm failed', [
                'trip' => $trip->getId(),
                'user' => $user->getId(),
                'error' => $e->getMessage(),
            ]);
            $this->addFlash('error', 'Impossible de confirmer la réservation : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_carpool_show', ['id' => $trip->getId()]);
    }

    #[Route('/espace/chauffeur/trajets/nouveau', name: 'app_cs_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        VehicleRepository $vehicleRepo
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if (!$this->isGranted('ROLE_DRIVER')) {
            $this->addFlash('error', 'Tu dois être chauffeur pour saisir un voyage.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $cs = new CarSharings();
        $form = $this->createForm(CarSharingType::class, $cs, ['driver' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $slugBase = sprintf('%s-%s-%s', $cs->getFromCity(), $cs->getToCity(), $cs->getDepartureAt()?->format('YmdHi'));
            $cs->setSlug(strtolower($slugger->slug($slugBase)));

            $cs->setDriverId($user);
            $cs->setSeatsRemaining(max(0, (int) $cs->getSeatsTotal()));

            $now = new \DateTime();
            $cs->setCreatedAt($now);
            $cs->setUpdatedAt($now);

            $chosenVehicle = $form->get('vehicleId')->getData();
            $wantNew = (bool) $form->get('addVehicle')->getData();

            if (!$chosenVehicle && $wantNew) {
                $vf = $form->get('vehicleQuick');
                $vehicle = new Vehicle();
                $vehicle->setBrand($vf->get('brand')->getData());
                $vehicle->setModel($vf->get('model')->getData());
                $vehicle->setColor($vf->get('color')->getData());
                $vehicle->setSeats((int) $vf->get('seats')->getData());
                $vehicle->setPlate($vf->get('plate')->getData());
                $vehicle->setFirstRegistration($vf->get('firstRegistration')->getData());
                $vehicle->setEnergy($vf->get('energy')->getData());
                $vehicle->setOwnerId($user);
                $vehicle->setSlug(strtolower($slugger->slug($vehicle->getBrand() . ' ' . $vehicle->getModel() . ' ' . $vehicle->getPlate())));
                $vehicle->setCreatedAt($now);
                $vehicle->setUpdatedAt($now);
                $em->persist($vehicle);
                $cs->setVehicleId($vehicle);
            } else {
                if ($chosenVehicle) {
                    if ($chosenVehicle->getOwnerId()?->getId() !== $user->getId()) {
                        $this->addFlash('error', 'Ce véhicule ne t’appartient pas.');
                        return $this->redirectToRoute('app_cs_new');
                    }
                    $cs->setVehicleId($chosenVehicle);
                } else {
                    $this->addFlash('error', 'Choisis un véhicule ou coche "Ajouter un nouveau véhicule".');
                    return $this->redirectToRoute('app_cs_new');
                }
            }

            if ($cs->getSeatsTotal() < 0) {
                $this->addFlash('error', 'Le nombre de places ne peut pas être négatif.');
                return $this->redirectToRoute('app_cs_new');
            }

            if (!$cs->getStatus()) {
                $cs->setStatus('published');
            }

            if ($cs->getVehicleId()?->getEnergy() && method_exists($cs->getVehicleId()->getEnergy(), 'isElectric')) {
                if ($cs->getVehicleId()->getEnergy()->isElectric()) {
                    $cs->setIsEco(true);
                }
            }

            try {
                $em->persist($cs);
                $em->flush();
                $this->addFlash('success', 'Trajet créé avec succès. Rappel : 2 crédits seront prélevés par la plateforme lors de la réservation.');
                return $this->redirectToRoute('app_user_dashboard');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Erreur enregistrement: ' . $e->getMessage());
            }
        }

        return $this->render('carSharings/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/covoiturages/{id}/annuler', name: 'app_carpool_cancel_as_driver', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelAsDriver(
        Request $request,
        CarSharings $trip,
        BookingsRepository $bookingsRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }
        if ($trip->getDriverId()?->getId() !== $user->getId()) {
            return $this->json(['ok' => false, 'message' => 'Tu n’es pas le chauffeur'], 403);
        }

        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('cancel_driver_' . $trip->getId(), $token)) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $status = strtolower((string) $trip->getStatus());
        if (in_array($status, ['canceled','finished'], true)) {
            return $this->json(['ok' => false, 'message' => 'Trajet déjà terminé ou annulé'], 400);
        }

        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $em->lock($trip, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($trip);

            $status = strtolower((string) $trip->getStatus());
            if (in_array($status, ['canceled','finished'], true)) {
                throw new \RuntimeException('Statut non annulable');
            }

            /** @var Bookings[] $confirmed */
            $confirmed = $bookingsRepo->findTripConfirmedBookings($trip);
            $costCredits = (int) ceil((float) $trip->getPrice());
            $now = new \DateTime();

            foreach ($confirmed as $bk) {
                $p = $bk->getPassenger();
                if ($p instanceof User) {
                    $p->setCredit((int)$p->getCredit() + $costCredits);
                    $em->persist($p);
                }
                $bk->setStatus('CANCELED_BY_DRIVER');
                $bk->setUpdatedAt($now);
                $em->persist($bk);
            }

            $trip->setSeatsRemaining((int)$trip->getSeatsTotal());
            $trip->setStatus('canceled');
            $trip->setUpdatedAt($now);
            $em->persist($trip);

            $em->flush();
            $conn->commit();

            return $this->json([
                'ok' => true,
                'message' => 'Trajet annulé. Tous les passagers ont été remboursés.',
                'seatsRemaining' => $trip->getSeatsRemaining(),
                'status' => $trip->getStatus(),
            ]);
        } catch (\Throwable $e) {
            $conn->rollBack();
            return $this->json(['ok' => false, 'message' => 'Erreur: '.$e->getMessage()], 500);
        }
    }

    #[Route('/covoiturages/{id}/quitter', name: 'app_carpool_leave_as_passenger', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leaveAsPassenger(
        Request $request,
        CarSharings $trip,
        BookingsRepository $bookingsRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }

        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('leave_trip_' . $trip->getId(), $token)) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $status = strtolower((string) $trip->getStatus());
        if (in_array($status, ['canceled','finished'], true)) {
            return $this->json(['ok' => false, 'message' => 'Trajet terminé/annulé'], 400);
        }

        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $em->lock($trip, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($trip);

            $booking = $bookingsRepo->findActiveBooking($user, $trip);
            if (!$booking) {
                throw new \RuntimeException('Aucune réservation active trouvée.');
            }

            $now = new \DateTime();
            $costCredits = (int) ceil((float) $trip->getPrice());
            $user->setCredit((int)$user->getCredit() + $costCredits);

            $trip->setSeatsRemaining((int)$trip->getSeatsRemaining() + (int)$booking->getSeatsBooked());
            $trip->setUpdatedAt($now);

            $booking->setStatus('CANCELED_BY_PASSENGER');
            $booking->setUpdatedAt($now);

            $em->persist($user);
            $em->persist($trip);
            $em->persist($booking);
            $em->flush();

            $conn->commit();

            return $this->json([
                'ok' => true,
                'message' => 'Tu as quitté le trajet. Tes crédits ont été remboursés.',
                'seatsRemaining' => $trip->getSeatsRemaining(),
            ]);
        } catch (\Throwable $e) {
            $conn->rollBack();
            return $this->json(['ok' => false, 'message' => 'Erreur: '.$e->getMessage()], 500);
        }
    }

    #[Route('/covoiturages/{id}/start', name: 'app_trip_start', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function startTrip(
        Request $request,
        CarSharings $trip,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }
        if ($trip->getDriverId()?->getId() !== $user->getId()) {
            return $this->json(['ok' => false, 'message' => 'Tu n’es pas le chauffeur'], 403);
        }

        if (!$this->isCsrfTokenValid('start_trip_' . $trip->getId(), (string)$request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $status = strtolower((string)$trip->getStatus());
        if (in_array($status, ['canceled','finished','started'], true)) {
            return $this->json(['ok' => false, 'message' => 'Statut incompatible'], 400);
        }

        $trip->setStatus('started');
        $trip->setStartedAt(new \DateTime());
        $trip->setUpdatedAt(new \DateTime());

        $em->persist($trip);
        $em->flush();

        return $this->json([
            'ok' => true,
            'message' => 'Trajet démarré',
            'status' => $trip->getStatus(),
            'startedAt' => $trip->getStartedAt()?->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('/covoiturages/{id}/finish', name: 'app_trip_finish', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function finishTrip(
        Request $request,
        CarSharings $trip,
        BookingsRepository $bookingsRepo,
        PassengerConfirmationsRepository $pcRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['ok' => false, 'message' => 'Non connecté'], 401);
        }
        if ($trip->getDriverId()?->getId() !== $user->getId()) {
            return $this->json(['ok' => false, 'message' => 'Tu n’es pas le chauffeur'], 403);
        }

        if (!$this->isCsrfTokenValid('finish_trip_' . $trip->getId(), (string)$request->request->get('_token'))) {
            return $this->json(['ok' => false, 'message' => 'CSRF invalide'], 400);
        }

        $status = strtolower((string)$trip->getStatus());
        if ($status !== 'started') {
            return $this->json(['ok' => false, 'message' => 'Le trajet doit être démarré'], 400);
        }

        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $em->lock($trip, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($trip);

            $trip->setStatus('finished');
            $trip->setFinishedAt(new \DateTime());
            $trip->setUpdatedAt(new \DateTime());
            $em->persist($trip);

            /** @var Bookings[] $confirmed */
            $confirmed = $bookingsRepo->findTripConfirmedBookings($trip);
            $now = new \DateTime();

            foreach ($confirmed as $bk) {
                $existing = $pcRepo->findOneBy(['bookingId' => $bk]);
                if ($existing) {
                    if (strtoupper((string)$existing->getStatus()) === 'PENDING') {
                    }
                } else {
                    $pc = (new \App\Entity\PassengerConfirmations())
                        ->setBookingId($bk)
                        ->setStatus('PENDING')
                        ->setToken(bin2hex(random_bytes(16)))
                        ->setCreatedAt($now)
                        ->setUpdatedAt($now);
                    $em->persist($pc);
                }
            }

            $em->flush();
            $conn->commit();

            return $this->json([
                'ok' => true,
                'message' => 'Trajet terminé. Confirmations créées pour les passagers.',
                'status' => $trip->getStatus(),
                'finishedAt' => $trip->getFinishedAt()?->format('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            $conn->rollBack();
            return $this->json(['ok' => false, 'message' => 'Erreur: '.$e->getMessage()], 500);
        }
    }
}
