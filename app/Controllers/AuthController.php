<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\DataObjects\RegisterUserData;
use App\DB;
use App\Exception\ValidationException;
use App\RequestValidators\NewPasswordRequestValidator;
use App\RequestValidators\RegisterUserRequestValidator;
use App\RequestValidators\UserLoginRequestValidator;
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
        private readonly DB $db,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth
    ) {
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'login.twig');
    }

    public function registerView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(RegisterUserRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->auth->register(
            new RegisterUserData($data['name'], $data['userName'], $data['email'], $data['password'])
        );

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (!$this->auth->attemptLogin($data)) {
            throw new ValidationException(['password' => ['You have entered an invalid username or password']]);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logOut(Request $request, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function renderNewPassword(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(NewPasswordRequestValidator::class)->validate(
            $_GET
        );

        $email = $_GET['email'];
        $token = $_GET['token'];;

        $dbToken = $this->db->getToken($email);

        if ($dbToken === $token) {
            return $this->twig->render($response, 'new-password.twig', ['get' => $_GET]);
        }

        return $response->withHeader('Location', '/')->withStatus(404);
    }

    public function setNewPassword(Request $request, Response $response)
    {
        $data = $this->requestValidatorFactory->make(SetPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->db->updateUserPassword($data['email'], $data['password']);

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function renderPasswordReset(Request $request, Response $response): Response
    {
        if (empty($_SESSION["user"])) {
            return $this->twig->render($response, 'password-reset.twig');
        }

        return $response->withHeader('Location', '/')->withStatus(404);
    }

    public function passwordReset(Request $request, Response $response)
    {
        // $data = $request->getParsedBody();
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $validate = $this->validator->validatePasswordReset($data);

        if ($validate !== true) {
            throw new ValidationException($validate);
        }

        $email = $data['email'];

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

        return $response;
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
