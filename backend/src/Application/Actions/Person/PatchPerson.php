<?php

declare(strict_types=1);

namespace App\Application\Actions\Person;

use Psr\Http\Message\ResponseInterface as Response;

class PatchPerson extends PersonAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        $id = (int) $this->resolveArg('id');

        $person = $this->personRepository->patch($id,$data);
        return $this->respondWithData($person);
        
    }
}