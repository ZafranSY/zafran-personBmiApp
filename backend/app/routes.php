<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\person\ListPersonsAction;
use App\Application\Actions\person\ListPersonById;
use App\Application\Actions\person\CreateNewPerson;
use App\Application\Actions\person\UpdatePerson;
use App\Application\Actions\person\PatchPerson;
use App\Application\Actions\person\DeletePerson;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
    
$app->group('/person', function (Group $group) {
        $group->get('', ListPersonsAction::class);
        $group->get('/{id}', ListPersonById::class);
        $group->post('/create', CreateNewPerson::class);
        $group->put('/update', UpdatePerson::class);
        $group->patch('/{id}', PatchPerson::class);
        $group->delete('/{id}', DeletePerson::class);
    });    
};
