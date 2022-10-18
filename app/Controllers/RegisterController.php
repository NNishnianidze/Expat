<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class RegisterController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        session_start();

        if (!empty($_SESSION["userEmail"])) {
            header('location: ../dashboard');
            exit();
        }

        return $this->twig->render($response, 'register.twig');
    }

    public function register(Request $request, Response $response)
    {
        $db = new DB;

        if (!isset($_POST['email']) || !isset($_POST['uid']) || !isset($_POST['pwd']) || !isset($_POST['name'])) {
            header('location: ../register?msg=emptyField');
            exit();
        }

        $userEmail = $db->validateUserEmail($_POST['email']);
        $userName = $db->validateUserName($_POST['uid']);

        if (!empty($userEmail)) {
            header('location: ../register?msg=invalidEmail');
            exit();
        }

        if (!empty($userName)) {
            header('location: ../register?msg=invalidUserName');
            exit();
        }

        if ($_POST['pwd'] !== $_POST['pwdrepeat']) {
            header('location: ../register?msg=passwordDontMatch');
        }

        if ($_POST['pwd'] === $_POST['pwdrepeat']) {
            $db->createUser($_POST('name'), $_POST['uid'], $_POST['email'], $_POST['pwd']);

            header('location: ../login?msg=successAccount');
            exit();
        }

        return $this->twig->render($response, '404.twig');
    }
}
