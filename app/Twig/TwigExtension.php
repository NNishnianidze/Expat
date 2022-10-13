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
            new TwigFunction('getMsg', [$this, 'getMsg']),
            new TwigFunction('getSession', [$this, 'getSession']),
        ];
    }

    public function getMsg(): string|null
    {
        if (!isset($_GET['msg'])) {
            return null;
        }

        $msg = $_GET['msg'];

        $return_value = match ($msg) {

            'invalidInput' =>
            '<div class="alert alert-danger" role="alert">
                <p>Invalid Email or Password!</p>
            </div>',

            'invalidEmail' =>
            '<div class="alert alert-danger" role="alert">
                <p>User with This Email Already Exists!</p>
            </div>',

            'invalidUserName' => '
            <div class="alert alert-danger" role="alert">
                <p>User with This Username Already Exists!</p>
            </div>',

            'success' =>
            '<div class="alert alert-success" role="alert">
                <p>Successfully created Account!</p>
            </div>',

            default => null,
        };

        return $return_value;
    }

    public function getSession(): string|null
    {
        #session_start();

        if (isset($_SESSION["userEmail"])) {
            return '<li class="nav-item"><a href="/logout" class="nav-link fw-bold fs-5" aria-current="page">Log Out</a></li>';
        }

        return null;
    }
}
