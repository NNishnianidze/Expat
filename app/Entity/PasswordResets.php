<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('password_resets')]
class PasswordResets
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $userEmail;

    #[Column(unique: true)]
    private string $token;

    #[Column]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): PasswordResets
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken($token): PasswordResets
    {
        $this->token = $token;

        return $this;
    }

    public function setCreatedAt(): PasswordResets
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
