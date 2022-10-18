<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('users')]
#[HasLifecycleCallbacks]
class Users
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column(unique: true)]
    private string $userName;

    #[Column(unique: true)]
    private string $email;

    #[Column]
    private string $password;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;

    #[OneToMany(mappedBy: 'user', targetEntity: Category::class)]
    private Collection $categories;

    #[OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    public function __construct()
    {
        $this->categories   = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): int
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Users
    {
        $this->name = $name;

        return $this;
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
        return $this->email;
    }

    public function setUserEmail(string $email): Users
    {
        $this->email = $email;

        return $this;
    }

    public function getUserPwd(): string
    {
        return $this->password;
    }

    public function setUserPwd($pwd): Users
    {
        $this->password = password_hash($pwd, PASSWORD_DEFAULT);

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Users
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Users
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategories(): ArrayCollection|Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): Users
    {
        $this->categories->add($category);

        return $this;
    }

    public function getTransactions(): ArrayCollection|Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): Users
    {
        $this->transactions->add($transaction);

        return $this;
    }
}
