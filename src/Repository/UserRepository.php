<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;


class UserRepository extends AbstractRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this->createQueryBuilder("user")
            ->addSelect("farm")
            ->leftJoin("user.farm", "farm")
            ->andWhere("user.email = :identifier")
            ->setParameter("identifier", $identifier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByIdentifierAndRefreshToken(string $identifier, mixed $refreshToken): ?User
    {
        return $this->createQueryBuilder("user")
            ->addSelect("farm")
            ->addSelect("refreshToken")
            ->leftJoin("user.farm", "farm")
            ->leftJoin("user.refreshTokens", "refreshToken")
            ->andWhere("user.email = :identifier")
            ->setParameter("identifier", $identifier)
            ->andWhere("refreshToken.token = :refreshToken")
            ->setParameter("refreshToken", $refreshToken)
            ->andWhere("refreshToken.expiresAt >= :expiresAt")
            ->setParameter("expiresAt", new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneUserByEmail(string $email): ?User
    {
        return $this->createQueryBuilder("user")
            ->andWhere("user.email = :email")
            ->setParameter("email", $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
