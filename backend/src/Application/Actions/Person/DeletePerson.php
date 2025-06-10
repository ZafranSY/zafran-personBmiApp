<?php

declare(strict_types=1);

namespace App\Application\Actions\Person;

use Psr\Http\Message\ResponseInterface as Response;

class DeletePerson extends PersonAction
{
    protected function action() : Response
    {
        $id = (int) $this->resolveArg('id');
        $person = $this->personRepository->delete($id);

                 return $this->respondWithData($person);

    }
}