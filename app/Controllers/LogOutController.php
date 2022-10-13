<?php

declare(strict_types=1);

namespace App\Controllers;

class LogOutController
{
    public function logOut()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../login');
        exit();
    }
}
