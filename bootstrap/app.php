<?php
/**
 * Created by PhpStorm.
 * User: Marcelo
 * Date: 8/2/2017
 * Time: 12:22 AM
 */
session_start();
require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    /**
     *MARCELO REMEMBER TO WHEN PUSH INTO PRODUCTION TO SET THIS TO FALSE
     *
     */
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

//needs to be require after instantiate slim app
require __DIR__ . '/../app/routes.php';

//instantiate slim container and attach twig to container
$container = $app->getContainer();
$container['view'] = function ($container){
  $view = new \Slim\Views\Twig(APP_ROOT. '\..\views\templates\partials',[

    /**
    *MARCELO REMEMBER TO WHEN PUSH INTO PRODUCTION TO POINT THIS TO A DIRECTORY
    *WHERE MY CACHE VIEWS WILL BE STORE
    */

        'cache' => false,
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->get('router'),
        $container->get('request')->getUri()
    ));
    return $view;
};
