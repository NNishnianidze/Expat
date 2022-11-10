<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class TransactionsController
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function index(Request $request, Response $response)
    {
        session_start();

        if (!empty($_SESSION["user"])) {
            return $this->twig->render($response, 'transactions.twig');
        }

        session_unset();
        header('location: ../login');
        exit();
    }
}
