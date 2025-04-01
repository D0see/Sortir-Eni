<?php

namespace App\Security;

use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SortietVoter extends Voter
{
    const ANNULER = 'annuler';
    const MODIFIER = 'modifier';
    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!in_array($attribute, [self::ANNULER, self::MODIFIER])){
            return false;
        }

        if(!$subject instanceof Sortie){
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $participant = $token->getUser();

        if(!$participant instanceof Participant){
            return false;
        }

        $sortie = $subject;

        return match($attribute) {
            self::ANNULER => $this->peutAnnuler($sortie, $participant),
            self::MODIFIER => $this->peutModifier($sortie, $participant),
            default => throw new \LogicException('This code should not be reached!')
        };

    }

    private function peutAnnuler(Sortie $sortie, Participant $participant): bool
    {
        if($sortie->getOrganisateur()!==$participant && !in_array('ROLE_ADMIN', $participant->getRoles())){
            return false;
        }
        return true;
    }

    private function peutModifier(Sortie $sortie, Participant $participant): bool
    {
        if($sortie->getOrganisateur()!==$participant && !in_array('ROLE_ADMIN', $participant->getRoles())){
            return false;
        }
        return true;
    }
}