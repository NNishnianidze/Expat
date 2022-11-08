<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response)
    {
        if (!empty($_SESSION["userEmail"])) {
            return $this->twig->render($response, 'dashboard.twig');
        }

        session_unset();
        header('location: ../login');
        exit();
    }
}
