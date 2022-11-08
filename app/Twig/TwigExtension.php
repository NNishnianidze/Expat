<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getSession', [$this, 'getSession']),
        ];
    }

    public function getSession(): string|null
    {
        if (isset($_SESSION["userEmail"])) {
            return '<li class="nav-item"><a href="/logout" class="nav-link fw-bold fs-5" aria-current="page">Log Out</a></li>';
        }

        return null;
    }
}
