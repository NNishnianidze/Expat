<?php

declare(strict_types=1);

namespace App;

use App\Entity\Users;
use App\Entity\PasswordResets;
use Doctrine\ORM\EntityManager;

class DB
{
    public function __construct(
        private EntityManager $entityManager,
    ) {
    }

    public function getUserPassword(string $email): bool|string
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.password')
            ->from(Users::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $password = $query->getOneOrNullResult();

        if (isset($password['password'])) {
            return $password['password'];
        }

        return false;
    }

    public function storeToken(string $email, string $token): void
    {
        $passwordReset = new PasswordResets;

        $passwordReset
            ->setEmail($email)
            ->setToken($token);

        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();
    }

    public function modifyToken(string $email, string $token): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->update(PasswordResets::class, 'pr')
            ->set('pr.token', ':token')
            ->where('pr.email = :email')
            ->setParameter('token', $token)
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $query->getOneOrNullResult();
    }

    public function getToken($email): bool|string
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('pr.token')
            ->from(PasswordResets::class, 'pr')
            ->where('pr.email = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $token = $query->getOneOrNullResult();

        if (is_null($token)) {
            return false;
        }

        return $token['token'];
    }

    public function updateUserPassword(string $email, string $password): void
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->update(Users::class, 'u')
            ->set('u.password', ':pwd')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setParameter('pwd', $password);

        $query = $queryBuilder->getQuery();

        $query->execute();

        $this->clearPasswordResetTable($email);
    }

    public function clearPasswordResetTable(string $email): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->delete(PasswordResets::class, 'pr')
            ->where('pr.email = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $query->execute();
    }

    public function validateEmailExistence(string $email): bool
    {
        if ($this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]) !== null) {
            return false;
        }

        return true;
    }

    public function validateUserName(string $username): bool|int
    {
        return !$this->entityManager->getRepository(Users::class)->count(['userName' => $username]);
    }

    public function vertifyPassword(string $email, string $value): bool
    {
        $userPassword = $this->getUserPassword($email);

        if (!$userPassword) {
            return false;
        }

        if (!password_verify($value, $userPassword)) {
            return false;
        }

        return true;
    }
}
