<?php
namespace App\Security;

use App\Entity\Participant;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participant) {
            return;
        }


        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return;
        }

        // Vérifier si l'utilisateur est actif
        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte est désactivé. Veuillez contacter l\'administrateur.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}
