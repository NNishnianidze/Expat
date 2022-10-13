<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManager;
use App\Entity\Users;
use App\EM;

class DB
{
    private $entityManager;

    public function getUserEmail(string $email): string
    {
        $this->entityManager = EM::getEntityManager();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userEmail')
            ->from(Users::class, 'u')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $userEmail = $query->getOneOrNullResult();

        return $userEmail['userEmail'];
    }

    public function getUserName(string $userName): string
    {
        $this->entityManager = EM::getEntityManager();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userName')
            ->from(Users::class, 'u')
            ->where('u.userName = :name')
            ->setParameter('name', $userName);

        $query = $queryBuilder->getQuery();

        $userName = $query->getOneOrNullResult();

        return $userName['userName'];
    }

    public function getUserPwd(string $email): string
    {
        $this->entityManager = EM::getEntityManager();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('u.userPwd')
            ->from(Users::class, 'u')
            ->where('u.userEmail = :email')
            ->setParameter('email', $email);

        $query = $queryBuilder->getQuery();

        $pwd = $query->getOneOrNullResult();

        return $pwd['userPwd'];
    }

    public function createUser(string $userName, string $userEmail, string $pwd)
    {
        $this->entityManager = EM::getEntityManager();

        $user = new Users;

        $user->setUserName($userName)
            ->setUserEmail($userEmail)
            ->setUserPwd($pwd)
            ->setCreatedAt()
            ->setActive();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
