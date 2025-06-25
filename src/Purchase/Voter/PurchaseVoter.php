<?php

namespace App\Purchase\Voter;

use App\Purchase\Entity\Purchase;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PurchaseVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ["READ", "UPDATE", "DELETE"]) && $subject instanceof Purchase;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Purchase $purchase */
        $purchase = $subject;

        if (!$purchase instanceof Purchase) {
            return false;
        }

        return $user->getUserFarm()->getId() === $purchase->getFarm()->getId();
    }
}
