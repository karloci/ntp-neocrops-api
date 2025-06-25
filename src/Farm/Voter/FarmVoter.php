<?php

namespace App\Farm\Voter;

use App\Farm\Entity\Farm;
use App\Purchase\Entity\Purchase;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class FarmVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ["READ", "UPDATE", "DELETE"]) && $subject instanceof Farm;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $farm = $subject;

        if (!$farm instanceof Farm) {
            return false;
        }

        return $user->getUserFarm()->getId() === $farm->getId();
    }
}
