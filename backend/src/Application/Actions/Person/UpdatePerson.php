<?php

declare (strict_types=1);
namespace App\Application\Actions\Person;
use Psr\Http\Message\ResponseInterface as Response;

class UpdatePerson extends PersonAction
{
    protected function action(): Response
    {
        $query_params = $this->request->getQueryParams();

     // Ensure 'id' is provided and cast it to an integer
        $id = isset($query_params['id']) ? (int) $query_params['id'] : null;
        
        if (!$id) {
            throw new \Slim\Exception\HttpBadRequestException($this->request, "Missing or invalid 'id' parameter.");
        }        $name = $query_params['name'] ?? null;
        $yob = $query_params['yob'] ?? null;
        $weight = $query_params['weight'] ?? null;
        $height = $query_params['height'] ?? null;
        $bmi = $query_params['bmi'] ?? null;
        $category = $query_params['category'] ?? null;
        $age = $query_params['age'] ?? null;

        $data= [
            'name' =>$name,
            'yob' =>$yob,
            'weight' => $weight,
            'height' => $height,
            'bmi' => $bmi,
            'category' => $category,
            'age' =>$age
        ];

        $person = $this->personRepository->update($id,$data);
        return $this->respondWithData($person);
    }
}
