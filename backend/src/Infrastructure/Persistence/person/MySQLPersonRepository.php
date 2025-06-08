<?php
namespace App\Infrastructure\Persistence\Person;

use App\Domain\Person\PersonRepository;
use App\Domain\Person\Person;
use App\Domain\Person\PersonNotFoundException;
use PDO;

class MySQLPersonRepository implements PersonRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM person");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?Person {
        $stmt = $this->pdo->prepare("SELECT * FROM person WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;

        return new Person(...array_values($data));
    }

    public function create(array $data): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO person (name, yob, weight, height, bmi, category, age)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'], $data['yob'], $data['weight'], $data['height'],
            $data['bmi'], $data['category'], $data['age']
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE person SET name = ?, yob = ?, weight = ?, height = ?, bmi = ?, category = ?, age = ? WHERE id = ?
        ");
        $stmt->execute([
            $data['name'], $data['yob'], $data['weight'], $data['height'],
            $data['bmi'], $data['category'], $data['age'], $id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function patch(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE person SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM person WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
