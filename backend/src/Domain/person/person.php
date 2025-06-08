<?php

namespace App\Domain\Person;

class Person {
    public function __construct(
        public int $id,
        public string $name,
        public int $yob,
        public float $weight,
        public float $height,
        public float $bmi,
        public string $category,
        public int $age
    ) {}
}
