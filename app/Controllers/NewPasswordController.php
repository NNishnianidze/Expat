<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use App\DBModel;

class NewPasswordController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        if (!isset($_GET['email'])) {
            header('location: ../password-reset?msg=newToken');
            exit();
        }

        if (!isset($_GET['token'])) {
            header('location: ../password-reset?msg=newToken');
            exit();
        }

        $db = new DB;

        $email = $_GET['email'];
        $token = $_GET['token'];;

        $dbToken = $db->getToken($email);

        if ($dbToken === $token) {
            return $this->twig->render($response, 'new-password.twig', ['get' => $_GET]);
            exit();
        }

        header('location: ../password-reset?msg=newToken');
        exit();
    }

    public function setNewPass()
    {
        $db = new DB;

        $email = $_POST['email'];
        $uri = '?email=' . $email;

        if (!isset($_POST['new_pass']) || !isset($_POST['new_pass_r'])) {
            header('location: ../new-password' . $uri . '&msg=emptyField');
            exit();
        }

        if ($_POST['new_pass'] !== $_POST['new_pass_r']) {
            header('location: ..' . $uri . '?msg=passwordDontMatch');
            exit();
        }

        $db->updateUserPwd($email, $_POST['new_pass']);

        header('location: ../login?msg=successPasswordReset');
        exit();
    }
}
