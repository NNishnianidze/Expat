<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManager;

class EM
{
    static function getEntityManager()
    {
        $container = require CONFIG_PATH . '/container/container.php';

        $entityManager = $container->get(EntityManager::class);

        return $entityManager;
    }
}
