<?php

namespace App\Controller;

use App\Entity\DriverPreferences;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\DriverPreferencesType;
use App\Form\RoleSelectionType;
use App\Form\VehicleType;
use App\Form\CustomPreferenceType;
use App\Repository\DriverPreferencesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/espace/utilisateur', name: 'app_user_dashboard', methods: ['GET','POST'])]
    public function dashboard(Request $request, EM $em, DriverPreferencesRepository $prefRepo): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $roleForm = $this->createForm(RoleSelectionType::class);
        $roleForm->get('driver')->setData(in_array('ROLE_DRIVER', $user->getRoles(), true));
        $roleForm->get('passenger')->setData(in_array('ROLE_PASSENGER', $user->getRoles(), true));
        $roleForm->handleRequest($request);

        if ($roleForm->isSubmitted() && $roleForm->isValid()) {
            $roles = array_diff($user->getRoles(), ['ROLE_DRIVER', 'ROLE_PASSENGER']);
            if ($roleForm->get('driver')->getData())    $roles[] = 'ROLE_DRIVER';
            if ($roleForm->get('passenger')->getData()) $roles[] = 'ROLE_PASSENGER';
            $user->setRoles(array_values(array_unique($roles)));
            $em->flush();
            $this->addFlash('success', 'Rôles mis à jour.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $vehicle = new Vehicle();
        $vehicleForm = $this->createForm(VehicleType::class, $vehicle);
        $vehicleForm->handleRequest($request);

        if ($vehicleForm->isSubmitted() && $vehicleForm->isValid()) {
            $vehicle->setOwnerId($user);
            $vehicle->setSlug(strtolower($vehicle->getBrand().'-'.$vehicle->getModel().'-'.substr(sha1(uniqid()),0,6)));
            $vehicle->setCreatedAt(new \DateTime());
            $vehicle->setUpdatedAt(new \DateTime());
            $em->persist($vehicle);
            $em->flush();
            $this->addFlash('success', 'Véhicule ajouté.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $prefForm = $this->createForm(DriverPreferencesType::class);

        $byKey = [];
        foreach ($user->getDriverPreferences() as $p) {
            $byKey[$p->getKeyName()] = $p;
        }
        $prefForm->get('smokerAllowed')->setData(($byKey['smoker'] ?? null)?->getValueText() === 'accepté');
        $prefForm->get('animalsAllowed')->setData(($byKey['animal'] ?? null)?->getValueText() === 'accepté');

        $prefForm->handleRequest($request);
        if ($prefForm->isSubmitted() && $prefForm->isValid()) {
            $smoker = $prefRepo->findOneBy(['driverId' => $user, 'keyName' => 'smoker'])
                ?? (new DriverPreferences())->setDriverId($user)->setKeyName('smoker');
            $smoker->setValueText($prefForm->get('smokerAllowed')->getData() ? 'accepté' : 'refusé');
            $em->persist($smoker);

            $animal = $prefRepo->findOneBy(['driverId' => $user, 'keyName' => 'animal'])
                ?? (new DriverPreferences())->setDriverId($user)->setKeyName('animal');
            $animal->setValueText($prefForm->get('animalsAllowed')->getData() ? 'accepté' : 'refusé');
            $em->persist($animal);

            $em->flush();
            $this->addFlash('success', 'Préférences mises à jour.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $newPref = new DriverPreferences();
        $customForm = $this->createForm(CustomPreferenceType::class, $newPref);
        $customForm->handleRequest($request);

        if ($customForm->isSubmitted() && $customForm->isValid()) {
            $reserved = ['smoker', 'animal'];
            if (in_array($newPref->getKeyName(), $reserved, true)) {
                $this->addFlash('danger', 'Cette clé est réservée. Utilise un autre nom.');
                return $this->redirectToRoute('app_user_dashboard');
            }

            $existing = $prefRepo->findOneBy(['driverId' => $user, 'keyName' => $newPref->getKeyName()]);
            if ($existing) {
                $existing->setValueText($newPref->getValueText());
            } else {
                $newPref->setDriverId($user);
                $em->persist($newPref);
            }

            $em->flush();
            $this->addFlash('success', 'Préférence personnalisée enregistrée.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/dashboard.html.twig', [
            'user'         => $user,
            'roleForm'     => $roleForm->createView(),
            'vehicleForm'  => $vehicleForm->createView(),
            'prefForm'     => $prefForm->createView(),
            'customForm'   => $customForm->createView(),
            'vehicles'     => $user->getVehicles(),
            'preferences'  => $user->getDriverPreferences(),
            'isOwner'      => true,
        ]);
    }

    #[Route('/utilisateur/{slug}', name: 'app_user_public', methods: ['GET'])]
    public function publicProfile(string $slug, UserRepository $users): Response
    {
        $profile = $users->findOneBy(['slug' => $slug]);
        if (!$profile) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/dashboard.html.twig', [
            'user'         => $profile,
            'roleForm'     => null,
            'vehicleForm'  => null,
            'prefForm'     => null,
            'customForm'   => null,
            'vehicles'     => $profile->getVehicles(),
            'preferences'  => $profile->getDriverPreferences(),
            'isOwner'      => false,
        ]);
    }

    #[Route('/espace/utilisateur/preference/{id}/supprimer', name: 'app_user_preference_delete', methods: ['POST'])]
    public function deletePreference(DriverPreferences $pref, Request $request, EM $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($pref->getDriverId() !== $this->getUser()) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['ok' => false, 'message' => 'Accès refusé'], 403);
            }
            throw $this->createAccessDeniedException();
        }

        if (in_array($pref->getKeyName(), ['smoker', 'animal'], true)) {
            $msg = 'Cette préférence ne peut pas être supprimée ici.';
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['ok' => false, 'message' => $msg], 400);
            }
            $this->addFlash('danger', $msg);
            return $this->redirectToRoute('app_user_dashboard');
        }

        if (!$this->isCsrfTokenValid('del_pref_'.$pref->getId(), $request->request->get('_token'))) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['ok' => false, 'message' => 'Token CSRF invalide'], 400);
            }
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $id = $pref->getId();
        $em->remove($pref);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['ok' => true, 'id' => $id, 'message' => 'Préférence supprimée.']);
        }

        $this->addFlash('success', 'Préférence supprimée.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
