<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use App\Domain\Person\PersonRepository;
use App\Infrastructure\Persistence\Person\MySQLPersonRepository;

$containerBuilder->addDefinitions([
    PersonRepository::class => function () {
        $pdo = require __DIR__ . '/../src/db_connection.php';
        return new MySQLPersonRepository($pdo);
    }
]);

$containerBuilder->addDefinitions([
    PersonRepository::class => function () {
        $pdo = getPDO(); // define this globally or in your settings
        return new MySQLPersonRepository($pdo);
    }
]);
return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};
