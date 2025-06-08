<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
require __DIR__ . '/../src/db_connection.php';
require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);

$app->options('/{routes:..+}', function(Request $request, Response $response)
{
	return $response;
});

// === Person Routes ===

// GET all persons
$app->get('/person', function ($request, $response) {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM person");
    $data = $stmt->fetchAll();

    $response->getBody()->write(json_encode($data));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


// GET person by ID
$app->get('/person/{id}', function ($request, $response, $args) {
    $id = (int) $args['id'];
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM person WHERE id = ?");
    $stmt->execute([$id]);
    $person = $stmt->fetch();

    if (!$person) {
        $response->getBody()->write(json_encode(['error' => "Person not found"]));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($person));
    return $response->withHeader('Content-Type', 'application/json');
});


// POST create person
$app->post('/person', function ($request, $response) {
    try {
        $pdo = getPDO();

        // Parse JSON input from request body
        $data = json_decode($request->getBody()->getContents(), true);

        // Extract and validate fields
        $name = $data['name'] ?? null;
        $yob = $data['yob'] ?? null;
        $weight = $data['weight'] ?? null;
        $height = $data['height'] ?? null;
        $bmi = $data['bmi'] ?? null;
        $category = $data['category'] ?? null;
        $age = $data['age'] ?? null;

        // Simple backend validation
        if (!$name || !$yob || !$weight || !$height) {
            $response->getBody()->write(json_encode(["error" => "Missing required fields"]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Insert into the database
        $stmt = $pdo->prepare("
            INSERT INTO person (name, yob, weight, height, bmi, category, age)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $yob, $weight, $height, $bmi, $category, $age]);

        // Response
        $response->getBody()->write(json_encode([
            "message" => "Person created successfully"
        ]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        // Handle DB errors
        $response->getBody()->write(json_encode([
            "error" => "Database error",
            "details" => $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});


// PUT update full person
$app->put('/person/{id}', function ($request, $response, $args) {
    $id = (int) $args['id'];
    $pdo = getPDO();

    $data = json_decode($request->getBody()->getContents(), true);

    $name = $data['name'] ?? null;
    $yob = $data['yob'] ?? null;
    $weight = $data['weight'] ?? null;
    $height = $data['height'] ?? null;
    $bmi = $data['bmi'] ?? null;
    $category = $data['category'] ?? null;
    $age = $data['age'] ?? null;

    if (!$name || !$yob || !$weight || !$height) {
        $response->getBody()->write(json_encode(["error" => "Missing required fields"]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $stmt = $pdo->prepare("
        UPDATE person SET name = ?, yob = ?, weight = ?, height = ?, bmi = ?, category = ?, age = ? WHERE id = ?
    ");
    $stmt->execute([$name, $yob, $weight, $height, $bmi, $category, $age, $id]);

    if ($stmt->rowCount() === 0) {
        $response->getBody()->write(json_encode(['error' => 'Person not found or no changes made']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['message' => "Person updated successfully"]));
    return $response->withHeader('Content-Type', 'application/json');
});

// PATCH partial update person
$app->patch('/person/{id}', function ($request, $response, $args) {
    $id = (int) $args['id'];
    $pdo = getPDO();
    $data = json_decode($request->getBody()->getContents(), true);

    if (!$data) {
        $response->getBody()->write(json_encode(['error' => 'No data provided']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $fields = [];
    $values = [];
    foreach ($data as $key => $value) {
        if (in_array($key, ['name', 'yob', 'weight', 'height', 'bmi', 'category', 'age'])) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
    }

    if (empty($fields)) {
        $response->getBody()->write(json_encode(['error' => 'No valid fields to update']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $values[] = $id;

    $sql = "UPDATE person SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    if ($stmt->rowCount() === 0) {
        $response->getBody()->write(json_encode(['error' => 'Person not found or no changes made']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['message' => 'Person updated successfully']));
    return $response->withHeader('Content-Type', 'application/json');
});


// DELETE person
$app->delete('/person/{id}', function ($request, $response, $args) {
    $id = (int) $args['id'];
    $pdo = getPDO();

    $stmt = $pdo->prepare("DELETE FROM person WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        $response->getBody()->write(json_encode(['error' => 'Person not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['message' => 'Person deleted successfully']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();