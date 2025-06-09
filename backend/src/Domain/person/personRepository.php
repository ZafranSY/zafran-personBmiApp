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


