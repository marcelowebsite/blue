<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

//import customers route
require '../src/routes/programs.php';
$app->run();