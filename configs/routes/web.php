<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TransactionsController;
use Slim\App;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index']);
    $app->get('/dashboard', [HomeController::class, 'index']);
    $app->get('/register', [AuthController::class, 'renderRegister']);
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/login', [AuthController::class, 'renderLogin']);
    $app->post('/login', [AuthController::class, 'validateUser']);
    $app->get('/logout', [AuthController::class, 'logOut']);
    $app->get('/password-reset', [AuthController::class, 'renderPasswordReset']);
    $app->post('/password-reset', [AuthController::class, 'resetPass']);
    $app->get('/new-password', [AuthController::class, 'renderNewPassword']);
    $app->post('/new-password', [AuthController::class, 'setNewPass']);
    $app->get('/dashboard/transactions', [TransactionsController::class, 'index']);
};
