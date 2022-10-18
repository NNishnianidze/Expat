<?php

declare(strict_types=1);

namespace App;

use App\Entity\Users;
use App\EM;
use App\Entity\PasswordResets;

class DB
{
    private $entityManager;

    public function __construct()
    {
        $this->entityManager = EM::getEntityManager();
    }

    public function validateUserEmail(string $email): string|array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userEmail')
            ->from(Users::class, 'u')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $userEmail = $query->getOneOrNullResult();

        return $userEmail['userEmail'] ?? [];
    }

    public function getUserNameFromEmail(string $email): string|array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userName')
            ->from(Users::class, 'u')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $userEmail = $query->getOneOrNullResult();

        return $userEmail['userName'] ?? [];
    }

    public function validateUserName(string $userName): string|array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userName')
            ->from(Users::class, 'u')
            ->where('u.userName = :name')
            ->setParameter('name', $userName);

        $query = $queryBuilder->getQuery();

        $userName = $query->getOneOrNullResult();

        return $userName['userName'] ?? [];
    }

    public function getUserPwd(string $email): string|array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userPwd')
            ->from(Users::class, 'u')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $pwd = $query->getOneOrNullResult();

        return $pwd['userPwd'] ?? [];
    }

    public function createUser(string $userName, string $userEmail, string $pwd): void
    {
        $user = new Users;

        $user->setUserName($userName)
            ->setUserEmail($userEmail)
            ->setUserPwd($pwd)
            ->setCreatedAt()
            ->setActive();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function storeToken(string $email, string $token): void
    {
        $passwordReset = new PasswordResets;

        $passwordReset
            ->setUserEmail($email)
            ->setToken($token)
            ->setCreatedAt();

        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();
    }

    public function modifyToken(string $email, string $token): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->update(PasswordResets::class, 'pr')
            ->set('pr.token', ':token')
            ->where('pr.userEmail = :email')
            ->setParameter('token', $token)
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $query->getOneOrNullResult();

        $this->setDateTime($email);
    }

    public function getToken($email): string|null
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('pr.token')
            ->from(PasswordResets::class, 'pr')
            ->where('pr.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $token = $query->getOneOrNullResult();

        return $token['token'] ?? null;
    }

    public function updateUserPwd(string $email, string $pwd): void
    {
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);

        $this->entityManager->clear();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->update(Users::class, 'u')
            ->set('u.userPwd', ':pwd')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email)
            ->setParameter('pwd', $pwd);

        $query = $queryBuilder->getQuery();

        $query->getOneOrNullResult();

        $this->clearPasswordResetTable($email);
    }

    public function clearPasswordResetTable(string $email): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->delete(PasswordResets::class, 'pr')
            ->where('pr.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $query->execute();
    }

    public function setDateTime(string $email): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $date = new \DateTime;

        $queryBuilder
            ->update(PasswordResets::class, 'pr')
            ->set('pr.createdAt', ':date')
            ->where('pr.userEmail = :email')
            ->setParameter('date', $date)
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $query->getOneOrNullResult();
    }
}
