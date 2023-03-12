<?php 
use DI\Container;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Carbon\Carbon;

require 'vendor/autoload.php';

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

$twig = Twig::create('./templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, $args) {

    $view = Twig::fromRequest($request);
    return $view->render($response, 'index.html', []);
})->setName('index');

$app->run();
