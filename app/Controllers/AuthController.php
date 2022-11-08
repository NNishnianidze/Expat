<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use App\Exception\ValidationException;
use App\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private DB $db,
        private Validator $validator,
    ) {
    }

    public function renderLogin(Request $request, Response $response): Response
    {
        if (!empty($_SESSION["userEmail"])) {
            header('location: ../dashboard');
            exit();
        }

        return $this->twig->render($response, 'login.twig');
    }

    public function renderRegister(Request $request, Response $response): Response
    {
        if (!empty($_SESSION["userEmail"])) {
            return $this->twig->render($response, 'dashboard.twig');
        }

        return $this->twig->render($response, 'register.twig');
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $validate = $this->validator->validateLogin($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $_SESSION["userEmail"] = $_POST["email"];

        return $this->twig->render($response, 'dashboard.twig');
    }

    public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $validate = $this->validator->validateRegister($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $this->db->createUser($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password']);
        return $this->twig->render($response, 'login.twig', ['succes' => true]);

        return $this->twig->render($response, '404.twig');
    }

    public function logOut()
    {
        session_unset();
        session_destroy();

        header('location: ../login');
        exit();
    }

    public function renderNewPassword(Request $request, Response $response): Response
    {
        $data = $_GET;

        $validate = $this->validator->validateNewPassword($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $email = $_GET['email'];
        $token = $_GET['token'];;

        $dbToken = $this->db->getToken($email);

        if ($dbToken === $token) {
            return $this->twig->render($response, 'new-password.twig', ['get' => $_GET]);
        }

        return $this->twig->render($response, '404.twig');
    }

    public function setNewPass(Request $request, Response $response)
    {
        $email = $_POST['email'];

        $data = $request->getParsedBody();

        $validate = $this->validator->validateNewPass($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $this->db->updateUserPwd($email, $_POST['password']);

        return $this->twig->render($response, 'login.twig', ['success' => true]);
    }

    public function renderPasswordReset(Request $request, Response $response): Response
    {
        if (empty($_SESSION["userEmail"])) {
            return $this->twig->render($response, 'password-reset.twig');
        }

        header('location: ../dashboard');
        exit();
    }

    public function passwordReset(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $validate = $this->validator->validatePasswordReset($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $email = $_POST['email'];

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

        return $this->twig->render($response, 'password-reset.twig', ['success' => true]);
    }

    public function getToken(string $email): string
    {
        $token = bin2hex(random_bytes(50));

        if ($this->db->getToken($email) !== null) {
            $this->db->modifyToken($email, $token);
            return $token;
        }

        $this->db->storeToken($email, $token);
        return $token;
    }
}
