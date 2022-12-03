<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\Contracts\MailInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\DataObjects\RegisterUserData;
use App\DB;
use App\Exception\ValidationException;
use App\RequestValidators\NewPasswordRequestValidator;
use App\RequestValidators\PasswordResetRequestValidator;
use App\RequestValidators\RegisterUserRequestValidator;
use App\RequestValidators\SetPasswordRequestValidator;
use App\RequestValidators\UserLoginRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly DB $db,
        private readonly MailInterface $mail,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth
    ) {
    }

    public function renderLogin(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'login.twig');
    }

    public function renderRegister(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(RegisterUserRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->auth->register(
            new RegisterUserData($data['name'], $data['username'], $data['email'], $data['password'])
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


    public function renderPasswordReset(Request $request, Response $response): Response
    {
        if (empty($_SESSION["user"])) {

            if (isset($_SESSION['errors'])) {
                unset($_SESSION['errors']);
                return $this->twig->render($response, 'password-reset.twig', ['errors' => ['email' => "Password reset link is expired please make new request"]]);
            }

            return $this->twig->render($response, 'password-reset.twig',);
        }

        return $response->withHeader('Location', '/')->withStatus(308);
    }

    public function passwordReset(Request $request, Response $response)
    {
        $data = $this->requestValidatorFactory->make(PasswordResetRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (!$this->auth->checkCredentials($data)) {
            throw new ValidationException(['email' => ['You have entered an invalid email']]);
        };

        $email = $data['email'];
        $token = $this->getToken($email);

        $this->mail->send($email, $token);

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function renderNewPassword(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(NewPasswordRequestValidator::class)->validate(
            $_GET
        );

        if (isset($_SESSION['reset'])) {
            $data = $this->requestValidatorFactory->make(NewPasswordRequestValidator::class)->validate(
                $_SESSION['reset']
            );
            unset($_SESSION['reset']);
        }


        if ($this->db->getToken($data['email']) !== $data['token']) {

            $_SESSION['errors'] = true;

            return $response->withHeader('Location', '/password-reset')->withStatus(308);
        }

        $_SESSION['reset'] = ['email' => $data['email'], 'token' => $data['token']];

        return $this->twig->render($response, 'new-password.twig');
    }

    public function setNewPassword(Request $request, Response $response)
    {
        $data = $this->requestValidatorFactory->make(SetPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->db->updateUserPassword($_SESSION['reset']['email'], $_SESSION['reset']['token']);
        unset($_SESSION['reset']);

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function getToken(string $email): string
    {
        $token = bin2hex(random_bytes(50));

        if (!$this->db->getToken($email)) {
            $this->db->storeToken($email, $token);
            return $token;
        }

        $this->db->modifyToken($email, $token);
        return $token;
    }
}
