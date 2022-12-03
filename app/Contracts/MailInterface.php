<?php

declare(strict_types=1);

namespace App\Contracts;

interface MailInterface
{
    public function send(string $email, string $token): void;
}
