<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\LogOutController;
use App\Controllers\NewPasswordController;
use App\Controllers\NotFoundController;
use App\Controllers\PasswordResetController;
use Slim\App;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index']);
    $app->get('/dashboard', [HomeController::class, 'index']);
    $app->get('/register', [RegisterController::class, 'index']);
    $app->post('/register', [RegisterController::class, 'register']);
    $app->get('/login', [LoginController::class, 'index']);
    $app->post('/login', [LoginController::class, 'validateUser']);
    $app->get('/logout', [LogOutController::class, 'logOut']);
    $app->get('/404', [NotFoundController::class, 'index']);
    $app->get('/password-reset', [PasswordResetController::class, 'index']);
    $app->post('/password-reset', [PasswordResetController::class, 'resetPass']);
    $app->get('/new-password', [NewPasswordController::class, 'index']);
    $app->post('/new-password', [NewPasswordController::class, 'setNewPass']);
};
