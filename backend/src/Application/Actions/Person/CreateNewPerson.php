<?php
 declare ( strict_types=1);

 namespace App\Application\Actions\Person;
 use Psr\Http\Message\ResponseInterface as Response;

 class CreateNewPerson extends PersonAction
 {
    protected function action(): Response
    {
        $queryParams=$this->request->getQueryParams();

         $name = $queryParams['name'] ?? null;
        $yob = $queryParams['yob'] ?? null;
        $weight = $queryParams['weight'] ?? null;
        $height = $queryParams['height'] ?? null;
        $bmi = $queryParams['bmi'] ?? null;
        $category = $queryParams['category'] ?? null;
        $age = $queryParams['age'] ?? null;
        
        // $name = $this->resolveArg("name");
        // $yob =$this->resolveArg("yob");
        // $weight = $this->resolveArg("weight");
        // $height = $this->resolveArg("height");
        // $bmi = $this->resolveArg("bmi");
        // $category = $this->resolveArg("category");
        // $age = $this->resolveArg("age");

        // $data[] = array($name, $yob, $weight, $height, $bmi, $category, $age);
         // Prepare the data array
        $data = [
            'name' => $name,
            'yob' => $yob,
            'weight' => $weight,
            'height' => $height,
            'bmi' => $bmi,
            'category' => $category,
            'age' => $age
        ];
        $person = $this->personRepository->create($data);
        return $this->respondWithData($person);
    }
 }