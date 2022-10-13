<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\LogOutController;
use App\Controllers\NotFoundController;
use PHPUnit\Util\Annotation\Registry;
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
};
