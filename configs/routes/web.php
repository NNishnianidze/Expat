<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TransactionsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index'])->add(AuthMiddleware::class);
    $app->get('/register', [AuthController::class, 'renderRegister'])->add(GuestMiddleware::class);
    $app->post('/register', [AuthController::class, 'register'])->add(GuestMiddleware::class);
    $app->get('/login', [AuthController::class, 'renderLogin'])->add(GuestMiddleware::class);
    $app->post('/login', [AuthController::class, 'logIn'])->add(GuestMiddleware::class);
    $app->get('/logout', [AuthController::class, 'logOut'])->add(AuthMiddleware::class);;
    $app->get('/password-reset', [AuthController::class, 'renderPasswordReset']);
    $app->post('/password-reset', [AuthController::class, 'passwordReset']);
    $app->get('/new-password', [AuthController::class, 'renderNewPassword']);
    $app->post('/new-password', [AuthController::class, 'setNewPassword']);
    $app->get('/dashboard/transactions', [TransactionsController::class, 'index']);
};
