<?php

namespace App\Domain\Person;

interface PersonRepository {
    public function findAll(): array;
    public function findById(int $id): ?Person;
    public function create(array $data): void;
    public function update(int $id, array $data): bool;
    public function patch(int $id, array $data): bool;
    public function delete(int $id): bool;
}

use App\Domain\Person\PersonRepository;
use App\Infrastructure\Persistence\Person\MySQLPersonRepository;

$containerBuilder->addDefinitions([
    PersonRepository::class => function () {
        $pdo = getPDO(); // define this globally or in your settings
        return new MySQLPersonRepository($pdo);
    }
]);
