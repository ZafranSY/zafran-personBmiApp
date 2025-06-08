<?php
namespace App\Application\Actions\Person;

use App\Domain\Person\PersonRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListPersonsAction {
    public function __construct(private PersonRepository $repo) {}

    public function __invoke(Request $request, Response $response): Response {
        $persons = $this->repo->findAll();
        $response->getBody()->write(json_encode($persons));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
