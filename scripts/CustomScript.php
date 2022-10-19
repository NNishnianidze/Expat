<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use App\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../configs/path_constants.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new DB;
