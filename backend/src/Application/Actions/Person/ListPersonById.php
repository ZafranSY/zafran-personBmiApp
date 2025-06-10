<?php

declare(strict_types=1);

namespace App\Application\Actions\Person;

// use App\Application\Actions\Action;
// use App\Domain\Person\PersonRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListPersonById extends PersonAction
{
    protected function action():Response
    {
        $personId = (int)$this->resolveArg("id");
        $person= $this->personRepository->findById($personId);

        $this->logger->info("user of id `${personId}` was viewed");

        return $this->respondWithData($person);
    }
}