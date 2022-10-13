<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('users')]
class Users
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(unique: true)]
    private string $userName;

    #[Column(unique: true)]
    private string $userEmail;

    #[Column]
    private string $userPwd;

    #[Column]
    private \DateTime $createdAt;

    #[Column]
    private bool $active;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): Users
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): Users
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserPwd(): string
    {
        return $this->userPwd;
    }

    public function setUserPwd($pwd): Users
    {
        $this->userPwd = password_hash($pwd, PASSWORD_DEFAULT);

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): Users
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive()
    {
        $this->active = false;

        return $this;
    }
}
