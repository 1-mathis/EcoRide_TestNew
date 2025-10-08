<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\EmployeeType;
use App\Form\Admin\UserSuspendType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/employes', name: 'admin_employees_')]
final class EmployeeController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET','POST'])]
    public function index(Request $req, UserRepository $users, EM $em, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(EmployeeType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $username = $form->get('username')->getData();
            $plain = $form->get('password')->getData();

            $u = new User();
            $u->setEmail($email);
            $u->setUsername($username);
            $u->setSlug(strtolower(preg_replace('~[^a-z0-9]+~', '-', $username)).'-'.substr(sha1(uniqid()),0,5));
            $u->setRoles(['ROLE_EMPLOYE']);
            $u->setPassword($hasher->hashPassword($u, $plain));
            $u->setCredit(0);
            $u->setAvgRating('0.00');
            $u->setStatus('active');
            $now = new \DateTime();
            $u->setCreatedAt($now);
            $u->setUpdatedAt($now);

            $em->persist($u);
            $em->flush();
            $this->addFlash('success', 'Employé créé.');
            return $this->redirectToRoute('admin_employees_index');
        }

        $list = $users->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults(100)
            ->getQuery()->getResult();

        return $this->render('admin/employees/index.html.twig', [
            'form' => $form->createView(),
            'users' => $list,
        ]);
    }

    #[Route('/{id}/suspend', name: 'suspend', methods: ['GET','POST'])]
    public function suspend(User $user, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserSuspendType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $reason = (string) $form->get('reason')->getData();
            $user->setStatus('suspended');
            $user->setSuspendedAt(new \DateTime());
            if ($reason) $user->setSuspendedReason($reason);
            $user->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Compte suspendu.');
            return $this->redirectToRoute('admin_employees_index');
        }

        return $this->render('admin/employees/_suspend_modal.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/reactivate', name: 'reactivate', methods: ['POST'])]
    public function reactivate(User $user, Request $req, EM $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->isCsrfTokenValid('reactivate_user_'.$user->getId(), $req->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_employees_index');
        }

        $user->setStatus('active');
        $user->setSuspendedAt(null);
        $user->setSuspendedReason(null);
        $user->setUpdatedAt(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'Compte réactivé.');
        return $this->redirectToRoute('admin_employees_index');
    }
}
