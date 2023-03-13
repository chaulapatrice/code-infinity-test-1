<?php

use DI\Container;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use MongoDB\Client as MongoDBClient;

require 'vendor/autoload.php';

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

$container->set('mongo', function () {

    $username = getenv('MONGO_USERNAME');
    $password = getenv('MONGO_PASSWORD');

    if (($username === false) || ($password === false)) return null;

    $client = new MongoDBClient(
        'mongodb://mongo:27017',
        [
            'username' => $username,
            'password' => $password,
        ],
    );

    return $client;
});

$twig = Twig::create('./templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, $args) {

    $context = array(
        'code' => 0
    );

    if ($request->getMethod() === 'POST') {

        $mongo = $this->get('mongo');

        if ($mongo) {
            $database = $mongo->test1;
            $people = $database->people;
            $payload = $request->getParsedBody();

            try {

                $first_name = trim($payload['first_name']);
                $last_name = trim($payload['last_name']);

                $date = explode('-',$payload['date_of_birth']);

                $date_of_birth = $date[2] . "/" . $date[1] . "/" . $date[0];


                $result = $people->insertOne([
                    'id_number' => $payload['id_number'],
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'date_of_birth' => $date_of_birth,
                ]);

                $context['code'] = 1;
                $context['message'] = 'Your details  have been submitted successfully.';
            } catch (Exception $e) {
                $context['code'] = -1;
                if ($e->getCode() === 11000)
                    $context['message'] = "Duplicate ID";
                else
                    $context['message'] = 'Apologies, we are experiencing a technical problem.';
            }
        } else {
            $context['code'] = -1;
            $context['message'] = 'Apologies, we are experiencing a technical problem.';
        }
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'index.html', $context);
})->setName('index');


$app->get('/people/{id_number}', function (Request $request, Response $response, array $args) {

    $mongo = $this->get('mongo');

    if (!$mongo) {
        $payload = json_encode(array(
            'person' => null
        ));
        $response->getBody()->write($payload);
        $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    };

    try {
        
        $database = $mongo->test1;
        $people = $database->people;
        $person = $people->findOne(['id_number' => $args['id_number']]);

        $data = array();
        if ($person)
            $data['person'] = $person->jsonSerialize();
        else
            $data['person'] = null;

        $payload = json_encode($data);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    } catch (Exception $e) {

        $payload = json_encode(array(
            'person' => null
        ));
        $response->getBody()->write($payload);
        $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
})->setName('people');

$app->run();
