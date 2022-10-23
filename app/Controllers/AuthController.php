<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DB;
use App\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Valitron\Validator;

class AuthController
{
    public function __construct(private readonly Twig $twig, private DB $db)
    {
        $this->db = new DB;
    }

    public function renderLogin(Request $request, Response $response): Response
    {
        session_start();

        if (!empty($_SESSION["userEmail"])) {
            header('location: ../dashboard');
            exit();
        }

        session_unset();
        return $this->twig->render($response, 'login.twig');
    }

    public function validateUser(Request $request, Response $response)
    {
        if (!isset($_POST['email']) || !isset($_POST['pwd'])) {
            header('location: ../login?msg=emptyField');
            exit();
        }

        $userEmail = $this->db->validateUserEmail($_POST['email']);

        if (empty($userEmail)) {
            header('location: ../login?msg=invalidInput');
            exit();
        }

        $userPwd = $this->db->getUserPwd($_POST['email']);

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

    public function logOut()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../login');
        exit();
    }

    public function renderNewPassword(Request $request, Response $response): Response
    {
        if (!isset($_GET['email'])) {
            header('location: ../password-reset?msg=newToken');
            exit();
        }

        if (!isset($_GET['token'])) {
            header('location: ../password-reset?msg=newToken');
            exit();
        }

        $email = $_GET['email'];
        $token = $_GET['token'];;

        $dbToken = $this->db->getToken($email);

        if ($dbToken === $token) {
            return $this->twig->render($response, 'new-password.twig', ['get' => $_GET]);
            exit();
        }

        header('location: ../password-reset?msg=newToken');
        exit();
    }

    public function setNewPass()
    {
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

        $this->db->updateUserPwd($email, $_POST['new_pass']);

        header('location: ../login?msg=successPasswordReset');
        exit();
    }

    public function renderPasswordReset(Request $request, Response $response): Response
    {
        if (empty($_SESSION["userEmail"])) {
            return $this->twig->render($response, 'password-reset.twig');
        }

        header('location: ../dashboard');
        exit();
    }

    public function resetPass(Request $request, Response $response)
    {
        if (!isset($_POST['email'])) {
            header('location: ../password-reset?msg=emptyField');
            exit();
        }

        $email = $_POST['email'];

        $userName = $this->db->getUserNameFromEmail($email);

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
        $token = bin2hex(random_bytes(50));

        if (($this->db->getToken($email) !== null)) {
            $this->db->modifyToken($email, $token);
            return $token;
        }

        $this->db->storeToken($email, $token);
        return $token;
    }

    public function renderRegister(Request $request, Response $response): Response
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
        // $data = $request->getParsedBody();

        // $v = new Validator($data);

        // $v->rule('required', ['name', 'uid', 'email', 'pwd', 'pwdrepeat']);
        // $v->rule('email', 'email');
        // $v->rule('equals', 'pwdrepeat', 'pwd')->label('Confirm Password');
        // $v->rule(
        //     fn ($field, $value, $params, $fields) => !$this->entityManager->getRepository(User::class)->count(
        //         ['email' => $value]
        //     ),
        //     'email'
        // )->message('User with the given email address already exists');

        // if (!$v->validate()) {
        //     throw new ValidationException($v->errors());
        // }

        if (!isset($_POST['email']) || !isset($_POST['uid']) || !isset($_POST['pwd']) || !isset($_POST['name'])) {
            header('location: ../register?msg=emptyField');
            exit();
        }

        $userEmail = $this->db->validateUserEmail($_POST['email']);
        $userName = $this->db->validateUserName($_POST['uid']);

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
            $this->db->createUser($_POST('name'), $_POST['uid'], $_POST['email'], $_POST['pwd']);

            header('location: ../login?msg=successAccount');
            exit();
        }

        return $this->twig->render($response, '404.twig');
    }
}
