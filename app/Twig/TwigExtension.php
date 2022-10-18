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

            'invalidUserName' =>
            '<div class="alert alert-danger" role="alert">
                <p>User with This Username Already Exists!</p>
            </div>',

            'successAccount' =>
            '<div class="alert alert-success" role="alert">
                <p>Successfully created Account!</p>
            </div>',

            'NotFoundEmail' =>
            '<div class="alert alert-danger" role="alert">
                <p>Sorry User with This Email Dont Exists!</p>
            </div>',

            'successPasswordReset' =>
            '<div class="alert alert-success" role="alert">
                <p>Your Password is Changed!</p>
            </div>',

            'passwordDontMatch' =>
            '<div class="alert alert-danger" role="alert">
                <p>Password Dont Match!</p>
            </div>',

            'emptyField' =>
            '<div class="alert alert-danger" role="alert">
                <p>Please Fill all Fields!</p>
            </div>',

            'successSendEmail' =>
            '<div class="alert alert-success" role="alert">
                <p>An Email was send to your adress! Please check your Inbox!</p>
            </div>',

            'newToken' =>
            '<div class="alert alert-danger" role="alert">
                <p>Password Reset link expired please make new request.</p>
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
