<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class PasswordResetController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        if (empty($_SESSION["userEmail"])) {
            return $this->twig->render($response, 'password-reset.twig');
        }

        header('location: ../dashboard');
        exit();
    }

    public function resetPass(Request $request, Response $response)
    {
        $db = new DB;

        if (!isset($_POST['email'])) {
            header('location: ../password-reset?msg=emptyField');
            exit();
        }

        $email = $_POST['email'];

        $userName = $db->getUserNameFromEmail($email);

        if (empty($userName)) {
            header('location: ../password-reset?msg=NotFoundEmail');
            exit();
        }

        $token = $this->getToken($email);

        $html = <<<HTMLBody
            <h1 >
                We recived a request to reset your password.
            </h1>
            <p>
              Use the link below to set up a new password for your account. 
              If you did not request to reset password, ignore this email 
              and the link will expire on it own.
            </p>
            <a class="btn btn-primary p-3 fw-700" href='http://localhost:8000/new-password?email=$email&token=$token'>Choose a new Password</a>
        HTMLBody;;

        $email = (new Email())
            ->from('support@expat.com')
            ->to($email)
            ->subject('Reset Password')
            ->html($html);

        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        $mailer = new Mailer($transport);

        $mailer->send($email);

        header('location: ../password-reset?msg=successSendEmail');
        exit();

        return $this->twig->render($response, '404.twig');
    }

    public function getToken(string $email): string
    {
        $db = new DB;

        $token = bin2hex(random_bytes(50));

        if (($db->getToken($email) !== null)) {
            $db->modifyToken($email, $token);
            return $token;
        }

        $db->storeToken($email, $token);
        return $token;
    }
}
