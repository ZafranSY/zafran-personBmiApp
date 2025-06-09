<?php
namespace App\Application\Actions\person;

use App\Domain\Person\personRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListPersonsAction extends personAction {
// public function __construct(private PersonRepository $repo) {}

    // public function __invoke(Request $request, Response $response): Response {
    //     $persons = $this->repo->findAll();
    //     $response->getBody()->write(json_encode($persons));
    //     return $response->withHeader('Content-Type', 'application/json');
    // }
    protected function action(): Response
    {
        $person = $this->personRepository->findAll();
        $this->logger->info("Person list was viewed");

        return $this->respondWithData($person);
    }


}
