<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class LoginController
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

        return $this->twig->render($response, 'login.twig');
    }

    public function validateUser(Request $request, Response $response)
    {
        $db = new DB;

        if (!isset($_POST['email']) || !isset($_POST['pwd'])) {
            header('location: ../login?msg=emptyField');
            exit();
        }

        $userEmail = $db->validateUserEmail($_POST['email']);

        if (empty($userEmail)) {
            header('location: ../login?msg=invalidInput');
            exit();
        }

        $userPwd = $db->getUserPwd($_POST['email']);

        if (!password_verify($_POST['pwd'], $userPwd)) {
            header('location: ../login?msg=invalidInput');
            exit();
        }

        if (password_verify($_POST['pwd'], $userPwd)) {
            session_start();
            $_SESSION["userEmail"] = $_POST["email"];

            header('location: ../dashboard');
            exit();
        }

        return $this->twig->render($response, '404.twig');
    }
}
