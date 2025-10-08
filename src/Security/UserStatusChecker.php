<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserStatusChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) return;

        if ($user->getSuspendedAt() !== null || $user->getStatus() === 'suspended') {
            $reason = $user->getSuspendedReason() ?: 'Votre compte est suspendu.';
            throw new CustomUserMessageAccountStatusException($reason);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
