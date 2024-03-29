<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('password_resets')]
#[HasLifecycleCallbacks]
class PasswordReset
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $email;

    #[Column(unique: true)]
    private string $token;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }

    public function getemail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): PasswordReset
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken($token): PasswordReset
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): PasswordReset
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): PasswordReset
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
