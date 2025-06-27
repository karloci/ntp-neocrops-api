<?php

namespace App\Consumption\Voter;

use App\Consumption\Entity\Consumption;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ConsumptionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ["READ", "UPDATE", "DELETE"]) && $subject instanceof Consumption;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Consumption $consumption */
        $consumption = $subject;

        if (!$consumption instanceof Consumption) {
            return false;
        }

        return $user->getUserFarm()->getId() === $consumption->getFarm()->getId();
    }
}
